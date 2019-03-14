<?php

namespace jakharbek\chat\repositories;

use jakharbek\chat\models\Chats;
use jakharbek\chat\models\ChatsUsers;
use jakharbek\chat\models\Messages;
use yii\base\Component;

/**
 * Class ChatRepository
 * @package jakharbek\chat\repositories
 */
class ChatRepository extends Component
{
    /**
     * @param $diolog_1
     * @param $diolog_2
     * @param $diolog_type
     * @return array|Chats|null
     */
    public function getChat($diolog_1, $diolog_2, $diolog_type)
    {
        $guid_1 = $diolog_1 . ":" . $diolog_2 . ":" . $diolog_type;
        $guid_2 = $diolog_2 . ":" . $diolog_1 . ":" . $diolog_type;

        return Chats::find()->where(['or', ['guid' => $guid_1], ['guid' => $guid_2]])->one();
    }

    /**
     * @param $user_id
     * @return array|Chats[]
     * Получение всех чатов пользователя
     */
    public function getChats($user_id, $status = ChatsUsers::STATUS_ACTIVE)
    {
        $chats = Chats::find()->joinWith("chatsUsers")->andWhere(['chats_users.status' => $status, 'chats_users.user_id' => $user_id])->all();
        return $chats;
    }

    /**
     * @param $chat_id
     * @return array|Messages|null
     */
    public function getLastSentMessage($chat_id)
    {
        return Messages::find()->chat($chat_id)->active()->orderBy(['message_id' => SORT_DESC])->one();
    }

    /**
     * @param $chat_id
     * @param int $status
     * @return array|Messages[]
     */
    public function getMessages($chat_id, $status = Messages::STATUS_SENT)
    {
        return $this->getMessagesQuery($chat_id, $status)->all();
    }

    /**
     * @param $chat_id
     * @param int $status
     * @return \jakharbek\chat\models\MessagesQuery
     */
    public function getMessagesQuery($chat_id, $status = Messages::STATUS_SENT)
    {
        return Messages::find()->chat($chat_id)->status($status)->orderBy(['message_id' => SORT_DESC]);
    }

}