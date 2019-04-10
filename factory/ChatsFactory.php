<?php

namespace jakharbek\chat\factory;

use jakharbek\chat\dto\linkedChatDTO;
use jakharbek\chat\models\Chats;
use jakharbek\chat\services\ChatServices;
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

            $chat->title = $createChatsDTO->title;
            $chat->owner_id = $createChatsDTO->owner_id;
            $chat->status = $createChatsDTO->status;
            $chat->type = $createChatsDTO->type;

            if ($chat->type == Chats::TYPE_PRIVATE) {
                if (count($createChatsDTO->members) !== 1) {
                    throw new \DomainException(Yii::t("main","The number of users can not be more or less than one."));
                }
            }

            if (!$chat->save()) {
                $transaction->rollBack();
                return $chat->getErrors();
            }

            $linkedChatDTO = new linkedChatDTO();
            $linkedChatDTO->chat_id = $chat->chat_id;
            $linkedChatDTO->user_id = Yii::$app->user->id;
            $linkedChatDTO->members = $createChatsDTO->members;

            /**
             * @var $chatServices ChatServices
             */
            $chatServices = Yii::createObject(ChatServices::class);
            $chatServices->linkedChat($linkedChatDTO);

            $transaction->commit();
        } catch (\Exception $exception) {
            $transaction->rollBack();
            throw new \DomainException($exception->getMessage());
        }

        return $chat;
    }
}