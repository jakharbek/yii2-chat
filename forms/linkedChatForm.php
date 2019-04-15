<?php

namespace jakharbek\chat\forms;

use jakharbek\chat\dto\createChatsDTO;
use jakharbek\chat\dto\linkedChatDTO;
use jakharbek\chat\factory\ChatsFactory;
use jakharbek\chat\models\Chats;
use jakharbek\chat\services\ChatServices;
use Yii;
use yii\base\Model;

/**
 * Class newPublicChat
 * @package jakharbek\chat\forms
 */
class linkedChatForm extends Model
{
    public $chat_id;
    public $members = [];

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['members', 'chat_id'], 'required'],
        ];
    }

    /**
     * @return array|bool|Chats
     * @throws \yii\base\InvalidConfigException
     */
    public function linked()
    {
        if (!$this->validate()) {
            return false;
        }

        /**
         * @var $chatServices ChatServices
         */
        $chatServices = Yii::createObject(ChatServices::class);
        $linkedChatDTO = new linkedChatDTO();
        $linkedChatDTO->chat_id = $this->chat_id;
        $linkedChatDTO->user_id = Yii::$app->user->id;
        $linkedChatDTO->members = $this->members;

        return $chatServices->linkedChat($linkedChatDTO);
    }
}