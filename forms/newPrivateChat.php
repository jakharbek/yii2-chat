<?php

namespace jakharbek\chat\forms;

use jakharbek\chat\dto\createChatsDTO;
use jakharbek\chat\factory\ChatsFactory;
use jakharbek\chat\models\Chats;
use Yii;
use yii\base\Model;

/**
 * Class newPrivateChat
 * @package jakharbek\chat\forms
 */
class newPrivateChat extends Model
{
    public $user_id;

    public function rules()
    {
        return [
            [['user_id'], 'required'],
        ];
    }

    public function create()
    {
        if (!$this->validate()) {
            return false;
        }

        /**
         * @var $chatFactory ChatsFactory
         */
        $chatFactory = Yii::createObject(ChatsFactory::class);

        $createChatDTO = new createChatsDTO();
        $createChatDTO->type = Chats::TYPE_PRIVATE;
        $createChatDTO->members[] = $this->user_id;
        $chat = $chatFactory::create($createChatDTO);

        if (!($chat instanceof Chats)) {
            $this->addError("user_id", Yii::t("main", "Chat is not created"));
            return false;
        }

        return $chat;
    }
}