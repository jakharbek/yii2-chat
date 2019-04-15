<?php

namespace jakharbek\chat\commands;

use common\models\UserTokens;
use jakharbek\chat\dto\sendMessageChatDTO;
use jakharbek\chat\exceptions\ChatException;
use jakharbek\chat\interfaces\iChatsRepository;
use jakharbek\chat\interfaces\iChatsServices;
use jakharbek\chat\models\Chats;
use jakharbek\chat\repositories\ChatRepository;
use jakharbek\chat\services\ChatServices;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use yii\helpers\Json;

/**
 * Class Chat
 * @package jakharbek\chat\commands
 */
class Chat implements MessageComponentInterface
{

    const TYPE_MESSAGE = "message";

    /**
     * @var \SplObjectStorage[]
     */
    protected $clients;

    /**
     * Chat constructor.
     */
    public function __construct()
    {
        $this->clients = [];
    }

    /**
     * @param ConnectionInterface $conn
     * @return mixed
     */
    protected function getConnectionParams(ConnectionInterface $conn)
    {
        /**
         * @var $httpRequest Request
         */
        $httpRequest = $conn->httpRequest;
        parse_str((string)$httpRequest->getUri()->getQuery(), $params);
        return $params;
    }

    /**
     * @param $access_token
     * @return bool
     */
    public function exsistUser(ConnectionInterface $conn)
    {
        $params = $this->getConnectionParams($conn);
        $access_token = $params['access_token'];
        $token = UserTokens::find()->andWhere(['token' => $access_token])->one();
        echo "User token is: " . $access_token;
        if (is_object($token)) {
            return true;
        }
        return false;
    }

    /**
     * @param ConnectionInterface $conn
     * @return int
     */
    public function getUserId(ConnectionInterface $conn)
    {
        $params = $this->getConnectionParams($conn);
        $access_token = $params['access_token'];

        echo "getUserID \n";

        return UserTokens::find()->andWhere(['token' => $access_token])->asArray()->one()['user_id'];
    }

    /**
     * @param ConnectionInterface $conn
     * @return bool
     */
    public function validateChat(ConnectionInterface $conn)
    {
        $params = $this->getConnectionParams($conn);

        /**
         * @var $repositoryChat iChatsRepository
         */
        $repositoryChat = \Yii::$container->get(iChatsRepository::class);
        try {
            $repositoryChat->getChatById($params['chat_id']);
            return true;
        } catch (ChatException $exception) {
            return false;
        }
    }


    /**
     * @param ConnectionInterface $connection
     * @return mixed
     */
    public function getChatIdFromConnection(ConnectionInterface $conn)
    {
        $params = $this->getConnectionParams($conn);

        /**
         * @var $repositoryChat ChatRepository
         */
        $repositoryChat = \Yii::$container->get(ChatRepository::class);
        if (!$this->validateChat($conn)) {
            return false;
        }

        return $repositoryChat->getChatById($params['chat_id'])->chat_id;
    }

    /**
     * @param ConnectionInterface $connection
     * @param $chat_id
     */
    public function attachToChat(ConnectionInterface $conn)
    {
        $chatId = $this->getChatIdFromConnection($conn);
        if (!array_key_exists($chatId, $this->clients)) {
            $this->clients[$chatId] = new \SplObjectStorage();
        }
        $this->clients[$chatId]->attach($conn);
        echo "attach to chat" . $chatId;
    }

    /**
     * @param ConnectionInterface $connection
     * @param $chat_id
     */
    public function detachFromChat(ConnectionInterface $conn)
    {
        $chatId = $this->getChatIdFromConnection($conn);
        if (array_key_exists($chatId, $this->clients)) {
            $this->clients[$chatId]->detach($conn);
            echo "detach to chat:" . $chatId;
        }
    }

    /**
     * @param ConnectionInterface $connection
     * @return bool
     */
    public function validateOpen(ConnectionInterface $conn)
    {
        if (!$this->exsistUser($conn)) {
            echo "User is not founded";
            return false;
        }

        if (!$this->validateChat($conn)) {
            echo "Chat validate is fail";
            return false;
        }

        return true;
    }


    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        if (!$this->validateOpen($conn)) {
            $conn->close();
        }
        echo "Connection is opened:" . print_r($this->getConnectionParams($conn), true);
        $this->attachToChat($conn);
    }

    /**
     * @param ConnectionInterface $from
     * @return bool|mixed|\SplObjectStorage
     */
    public function getClientsFromConnection(ConnectionInterface $from)
    {
        $chatId = $this->getChatIdFromConnection($from);
        if (array_key_exists($chatId, $this->clients)) {
            return $this->clients[$chatId];
        }
        return false;
    }

    /**
     * @param ConnectionInterface $from
     * @param string $msg
     *
     * @description
     * ```php
     * Connection is opened:Array
     * (
     * [access_token] => FpaEBocsTtCpzfbi7AqVvcgWQ5OOJF40
     * [diolog_1] => 1
     * [diolog_2] => 2
     * [diolog_type] => personal
     * [to_user_id] =>
     * )
     * attach to chat25start send messasgeArray
     * (
     * [type] => message
     * [data] => sadsd
     * )
     *
     * php
     */
    public function onMessage(ConnectionInterface $from, $_request)
    {
        echo "start send messasge";

        try {

            $params = $this->getConnectionParams($from);

            /**
             * @var $clients ConnectionInterface[]
             */
            $clients = $this->getClientsFromConnection($from);

            if (!$clients) {
                echo "Clients not founded";
                return false;
            }
            echo "json decoding";

            $request = Json::decode($_request);
            $from_user_id = $this->getUserId($from);
            $message = $request['data'];
            $replay_message_id = @$params['replay_message_id'];
            $chat_id = $params['chat_id'];

            if ($request['type'] == self::TYPE_MESSAGE) {


                /**
                 * @var $serviceChat iChatsServices
                 */
                $serviceChat = \Yii::$container->get(iChatsServices::class);

                $sendMessageChatDTO = new sendMessageChatDTO();
                $sendMessageChatDTO->chat_id = $chat_id;
                $sendMessageChatDTO->from_user_id = $from_user_id;
                $sendMessageChatDTO->message = $message;
                $sendMessageChatDTO->replay_message_id = $replay_message_id;
                $_message = $serviceChat->sendMessage($sendMessageChatDTO);

                foreach ($clients as $client) {
                    $response = [
                        'type' => self::TYPE_MESSAGE,
                        'data' => [
                            '_request' => $_request,
                            'replay_message_id' => $replay_message_id,
                            'from_user_id' => $from_user_id,
                            'message' => $message,
                            '_from' => $from,
                            '_message' => $_message
                        ]
                    ];
                    echo "Connection is opened:";

                    $client->send(Json::encode($response));
                }
            }
            echo "end send messasge";

        } catch (\Exception $exception) {
            echo "Error:" . print_r([
                    'error' => 1,
                    'data' => [$exception->getMessage(), $exception->getFile(), $exception->getLine(),$exception->getTraceAsString()],
                ], true);

            return $from->send(Json::encode([
                'error' => 1,
                'data' => [$exception->getMessage(), $exception->getFile(), $exception->getLine(),$exception->getTraceAsString()],
            ]));
        }
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        echo "Connection is closed:" . print_r($this->getConnectionParams($conn), true);

        $this->detachFromChat($conn);
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Connection has error:" . $e->getMessage();
        echo $e->getMessage();
        echo $e->getFile();
        echo $e->getLine();
        $this->detachFromChat($conn);
    }
}