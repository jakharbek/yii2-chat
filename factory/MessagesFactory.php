<?php

namespace jakharbek\chat\factory;

use jakharbek\chat\models\Messages;
use jakharbek\chat\dto\createMessagesDTO;
use yii\base\Component;
use Yii;
/**
 * Class MessagesFactory
 * @package jakharbek\chat\factory
 */
class MessagesFactory extends Component
{
    /**
     * @param createMessagesDTO $createMessagesDTO
     * @return mixed
     */
    public static function create(createMessagesDTO $createMessagesDTO)
    {
        /**
         * @var $message Messages
         */
        $message = Yii::createObject([
            'class' => Messages::class
        ]);

        $message->message = $createMessagesDTO->message;
        $message->type = $createMessagesDTO->type;
        $message->to_chat_id = $createMessagesDTO->to_chat_id;
        $message->to_user_id = $createMessagesDTO->to_user_id;
        $message->replay_message_id = $createMessagesDTO->replay_message_id;
        $message->from_user_id = $createMessagesDTO->from_user_id;

        if (!$message->save()) {
            return $message->getErrors();
        }

        return $message;
    }
}