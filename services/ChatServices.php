<?php

namespace jakharbek\chat\services;

use jakharbek\chat\dto\createChatsDTO;
use jakharbek\chat\dto\createMessagesDTO;
use jakharbek\chat\dto\sendMessageChatDTO;
use jakharbek\chat\exceptions\MessageСannotBeCreatedException;
use jakharbek\chat\factory\ChatsFactory;
use jakharbek\chat\factory\MessagesFactory;
use jakharbek\chat\interfaces\iChatsServices;
use jakharbek\chat\models\Chats;
use jakharbek\chat\models\ChatsUsers;
use jakharbek\chat\models\Messages;
use jakharbek\chat\repositories\ChatRepository;
use yii\base\BaseObject;
use yii\base\Component;
use \jakharbek\chat\exceptions\СhatСannotBeCreatedException;
use Yii;

/**
 * Class ChatServices
 * @package jakharbek\services
 */
class ChatServices extends Component implements iChatsServices
{
    /**
     * @var ChatRepository
     */
    public $chatRepository;

    /**
     * @var ChatsFactory
     */
    public $chatsFactory;

    /**
     * @var MessagesFactory
     */
    public $messagesFactory;

    /**
     * ChatServices constructor.
     * @param ChatRepository $chatRepository
     * @param ChatsFactory $chatsFactory
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->chatRepository = Yii::$container->get(ChatRepository::class);
        $this->chatsFactory = Yii::$container->get(ChatsFactory::class);
        $this->messagesFactory = Yii::$container->get(MessagesFactory::class);
        parent::__construct($config);
    }

    /**
     * @param sendMessageChatDTO $sendMessageChatDTO
     * @return mixed
     * @throws MessageСannotBeCreatedException
     * @throws \yii\base\InvalidConfigException
     * @throws СhatСannotBeCreatedException
     */
    public function sendMessage(sendMessageChatDTO $sendMessageChatDTO)
    {
        /**
         * @var $chat Chats
         */
        $chat = $this->chatRepository->getChat($sendMessageChatDTO->diolog_1, $sendMessageChatDTO->diolog_2, $sendMessageChatDTO->diolog_type);

        if (!($chat instanceof Chats)) {
            $createChatsDTO = new createChatsDTO();
            $createChatsDTO->diolog_1 = $sendMessageChatDTO->diolog_1;
            $createChatsDTO->diolog_2 = $sendMessageChatDTO->diolog_2;
            $createChatsDTO->diolog_type = $sendMessageChatDTO->diolog_type;
            $createChatsDTO->status = Chats::STATUS_ACTIVE;
            $createChatsDTO->type = Chats::TYPE_PRIVATE;
            $chat = $this->chatsFactory::create($createChatsDTO);

            if (!($chat instanceof Chats)) {
                throw new СhatСannotBeCreatedException(print_r($chat, true));
            }
        }

        $createMessagesDTO = new createMessagesDTO();
        $createMessagesDTO->from_user_id = $sendMessageChatDTO->from_user_id;
        $createMessagesDTO->message = $sendMessageChatDTO->message;
        $createMessagesDTO->replay_message_id = $sendMessageChatDTO->replay_message_id;
        $createMessagesDTO->to_user_id = $sendMessageChatDTO->to_user_id;
        $createMessagesDTO->to_chat_id = $chat->chat_id;
        $createMessagesDTO->type = Messages::TYPE_USUALLY;

        $message = $this->messagesFactory::create($createMessagesDTO);

        if (!($message instanceof Messages)) {
            throw new MessageСannotBeCreatedException(print_r($message, true));
        }

        $chat->link("messages", $message);

        return $message;
    }

    /**
     * @param $messages_id
     * @param null $user_id
     * @return Messages[]
     */
    public function setSeenMessagesById($messages_id, $user_id = null)
    {
        $messages_query = Messages::find()->andWhere(['message_id' => $messages_id]);
        if ($messages_query->count() == 0) {
            throw new \DomainException("Messages not founded");
        }

        /**
         * @var $messages Messages[]
         */
        $messages = $messages_query->all();

        foreach ($messages as $message) {
            if (!($message->to_user_id == $user_id|| $message->from_user_id == $user_id)) {
                throw new \DomainException("Access denied");
            }
            $message->setSeen($user_id);
        }

        return $messages;
    }

    /**
     * @param $message_id
     * @param $user_id
     * @param bool $deleteAll
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deleteMessage($message_id, $user_id, $delete_for, $deleteAll = false)
    {
        $message = Messages::findOne($message_id);
        if ($message->from_user_id == $user_id) {
            if($message->from_user_id == $delete_for){
                $message->updateAttributes(['from_status' => Messages::STATUS_DELETED]);
            }elseif($message->to_user_id == $delete_for){
                $message->updateAttributes(['to_status' => Messages::STATUS_DELETED]);
            }
            if($deleteAll){
                $message->updateAttributes(['from_status' => Messages::STATUS_DELETED]);
                $message->updateAttributes(['to_status' => Messages::STATUS_DELETED]);
                $message->delete();
            }
        }else{
            if($message->from_user_id == $delete_for){
                throw new \DomainException("You can not delete message for sender");
            }elseif($message->to_user_id == $delete_for){
                $message->updateAttributes(['to_status' => Messages::STATUS_DELETED]);
            }
            if($deleteAll){
                throw new \DomainException("You can not delete message for all");
            }
        }
        return $message;
    }
}