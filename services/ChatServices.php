<?php

namespace jakharbek\chat\services;

use jakharbek\chat\dto\createMessagesDTO;
use jakharbek\chat\dto\deleteMessageDTO;
use jakharbek\chat\dto\linkedChatDTO;
use jakharbek\chat\dto\sendMessageChatDTO;
use jakharbek\chat\dto\setSeenMessagesDTO;
use jakharbek\chat\exceptions\ChatException;
use jakharbek\chat\exceptions\MessageСannotBeCreatedException;
use jakharbek\chat\factory\ChatsFactory;
use jakharbek\chat\factory\MessagesFactory;
use jakharbek\chat\interfaces\iChatsRepository;
use jakharbek\chat\interfaces\iChatsServices;
use jakharbek\chat\models\ChatsUserGroup;
use jakharbek\chat\models\ChatsUserUser;
use jakharbek\chat\models\Messages;
use yii\base\Component;
use Yii;

/**
 * Class ChatServices
 * @package jakharbek\services
 */
class ChatServices extends Component implements iChatsServices
{
    /**
     * @var iChatsRepository
     */
    public $chatRepository;

    public function __construct(array $config = [])
    {
        $this->chatRepository = Yii::$container->get(iChatsRepository::class);
        parent::__construct($config);
    }

    /**
     * @param linkedChatDTO $linkedChatDTO
     * @return mixed
     */
    public function linkedChat(linkedChatDTO $linkedChatDTO)
    {
        $chat = $this->chatRepository->getChatById($linkedChatDTO->chat_id);
        if ($chat->isPrivate) {
            $member = current($linkedChatDTO->members);
            $query = ChatsUserUser::find()
                ->orWhere(['user_id_1' => $member, 'user_id_2' => Yii::$app->user->id, 'chat_id' => $chat->chat_id])
                ->orWhere(['user_id_2' => $member, 'user_id_1' => Yii::$app->user->id, 'chat_id' => $chat->chat_id]);
            if ($query->exists()) {
                return $query->one();
            }
            $chatUserUser = new ChatsUserUser();
            $chatUserUser->user_id_1 = $linkedChatDTO->user_id;
            $chatUserUser->user_id_2 = $member;
            $chatUserUser->chat_id = $chat->chat_id;
            if (!$chatUserUser->save()) {
                throw new ChatException("User is not linked to chat");
            }
            return $chatUserUser;
        }

        if ($chat->isPublic) {
            foreach ($linkedChatDTO->members as $member) {
                if (ChatsUserGroup::find()->andWhere(['user_id' => $member, 'chat_id' => $chat->chat_id])->exists()) {
                    continue;
                }
                $chatUserGroup = new ChatsUserGroup();
                $chatUserGroup->chat_id = $chat->chat_id;
                $chatUserGroup->user_id = $member;
                if (!$chatUserGroup->save()) {
                    throw new ChatException("User is not linked to chat");
                }
            }
            return true;
        }

        return false;
    }

    /**
     * @param sendMessageChatDTO $sendMessageChatDTO
     * @return mixed
     */
    public function sendMessage(sendMessageChatDTO $sendMessageChatDTO)
    {
        $chat = $this->chatRepository->getChatById($sendMessageChatDTO->chat_id);

        /**
         * @var $messagesFactory MessagesFactory
         */
        $messagesFactory = Yii::createObject(MessagesFactory::class);
        $createMessageDTO = new createMessagesDTO();
        $createMessageDTO->message = $sendMessageChatDTO->message;
        $createMessageDTO->from_user_id = $sendMessageChatDTO->from_user_id;
        $createMessageDTO->replay_message_id = $sendMessageChatDTO->replay_message_id;
        $createMessageDTO->type = $sendMessageChatDTO->type;

        /**
         * @var $message Messages
         */
        $message = $messagesFactory::create($createMessageDTO);

        if (!($message instanceof Messages)) {
            throw new MessageСannotBeCreatedException(Yii::t("main", "Message can not be created"));
        }

        return $message;
    }

    /**
     * @param setSeenMessagesDTO $seenMessagesDTO
     * @return mixed
     */
    public function setSeenMessages(setSeenMessagesDTO $seenMessagesDTO)
    {
        $messages_query = Messages::find()->andWhere(['message_id' => $seenMessagesDTO->message_ids]);
        if ($messages_query->count() == 0) {
            throw new ChatException("Messages not founded");
        }
        /**
         * @var $messages Messages[]
         */
        $messages = $messages_query->all();
        foreach ($messages as $message) {
            $message->setSeen($seenMessagesDTO->user_id);
        }
        return $messages;
    }

    /**
     * @param deleteMessageDTO $deleteMessageDTO
     * @return mixed
     */
    public function deleteMessage(deleteMessageDTO $deleteMessageDTO)
    {
        $message_id = $deleteMessageDTO->message_id;
        $user_id = $deleteMessageDTO->user_id;
        $delete_for = $deleteMessageDTO->delete_for;

        $message = Messages::findOne($message_id);

        switch ($deleteMessageDTO->delete_for) {
            case Messages::DELETE_FOR_ALL :
                $message->deleteForAll($deleteMessageDTO);
                break;
            case Messages::DELETE_FOR_SENDER :
                $message->deleteForSender($deleteMessageDTO);
                break;
            case Messages::DELETE_FOR_RECEIVER :
                $message->deleteForReceiver($deleteMessageDTO);
                break;
        }

        return $message;
    }
}