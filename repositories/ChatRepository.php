<?php

namespace jakharbek\chat\repositories;

use jakharbek\chat\dto\getChatDTO;
use jakharbek\chat\dto\getChatsDTO;
use jakharbek\chat\dto\getLastMessageDTO;
use jakharbek\chat\dto\getMessagesDTO;
use jakharbek\chat\exceptions\ChatException;
use jakharbek\chat\interfaces\iChatsRepository;
use jakharbek\chat\models\Chats;
use jakharbek\chat\models\ChatsUserUser;
use jakharbek\chat\models\Messages;
use yii\base\Component;

/**
 * Class ChatRepository
 * @package jakharbek\chat\repositories
 */
class ChatRepository extends Component implements iChatsRepository
{

    /**
     * @param getChatsDTO $getChatsDTO
     * @return mixed
     */
    public function getChats(getChatsDTO $getChatsDTO)
    {
        $query = Chats::find()->andFilterWhere(['label' => $getChatsDTO->label]);
        $query->andFilterWhere(['type' => $getChatsDTO]);

        if ($getChatsDTO->type == Chats::TYPE_PUBLIC) {
            $query->joinWith('chatsUserGroup');
            $query->andWhere(['chats_user_group.user_id' => $getChatsDTO->user_id]);
        }

        if ($getChatsDTO->type == Chats::TYPE_PRIVATE) {
            $query->andWhere(['chats.owner_id' => $getChatsDTO->user_id]);
        }

        if ($getChatsDTO->getQuery) {
            return $query;
        }

        return $query->all();
    }

    /**
     * @param getLastMessageDTO $getLastMessageDTO
     * @return mixed
     */
    public function getLastMessage(getLastMessageDTO $getLastMessageDTO)
    {
        return Messages::find()->chat($getLastMessageDTO->chat_id)->active()->orderBy(['message_id' => SORT_DESC])->one();
    }

    /**
     * @param getMessagesDTO $getMessagesDTO
     * @return mixed
     */
    public function getMessages(getMessagesDTO $getMessagesDTO)
    {
        $start_date = null;
        $end_date = null;

        if ($getMessagesDTO->forDate !== null) {
            $start_date = strtotime($getMessagesDTO->forDate);
            $end_date = strtotime($getMessagesDTO->forDate) + 86400;
        }

        $query = Messages::find()->chat($getMessagesDTO->chat_id)->status($getMessagesDTO->status)->andFilterWhere(['BETWEEN', 'created_at', $start_date, $end_date])->orderBy(['message_id' => SORT_DESC]);

        if ($getMessagesDTO->getQuery) {
            return $query;
        }

        return $query->all();
    }

    /**
     * @param $chat_id
     * @return Chats|null
     */
    public function getChatById($chat_id): ?Chats
    {
        $chat = Chats::findOne($chat_id);
        if (!($chat instanceof Chats)) {
            throw new ChatException("Chat is not founded");
        }
        return $chat;
    }

    /**
     * @param $message_id
     * @return Messages|null
     * @throws ChatException
     */
    public function getMessageById($message_id): ?Messages
    {
        $message = Messages::findOne($message_id);
        if (!($message instanceof Messages)) {
            throw new ChatException("Message is not founded");
        }
        return $message;
    }

}