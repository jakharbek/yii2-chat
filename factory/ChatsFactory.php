<?php

namespace jakharbek\chat\factory;

use jakharbek\chat\models\Chats;
use jakharbek\chat\models\ChatsUsers;
use Yii;
use \jakharbek\chat\dto\createChatsDTO;

/**
 * Class ChatsFactory
 * @package jakharbek\chat\factory
 */
class ChatsFactory
{
    /**
     * @param createChatsDTO $createChatsDTO
     * @return array|Chats
     */
    public static function create(createChatsDTO $createChatsDTO)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {

            /**
             * @var $chat Chats
             */
            $chat = Yii::createObject([
                'class' => Chats::class
            ]);

            $chat->diolog_1 = $createChatsDTO->diolog_1;
            $chat->diolog_2 = $createChatsDTO->diolog_2;
            $chat->diolog_type = $createChatsDTO->diolog_type;
            $chat->status = $createChatsDTO->status;
            $chat->type = $createChatsDTO->type;


            if (!$chat->save()) {
                $transaction->rollBack();
                return $chat->getErrors();
            }


            $chatsUsers = new ChatsUsers();
            $chatsUsers->user_id = $createChatsDTO->diolog_1;
            $chatsUsers->chat_id = $chat->chat_id;
            $chatsUsers->status = Chats::STATUS_ACTIVE;
            $chatsUsers->save();

            $chatsUsers2 = new ChatsUsers();
            $chatsUsers2->user_id = $createChatsDTO->diolog_2;
            $chatsUsers2->chat_id = $chat->chat_id;
            $chatsUsers2->status = Chats::STATUS_ACTIVE;
            $chatsUsers2->save();

            $transaction->commit();
        } catch (\Exception $exception) {
            $transaction->rollBack();
            throw new \DomainException($exception->getMessage());
        }

        return $chat;
    }
}