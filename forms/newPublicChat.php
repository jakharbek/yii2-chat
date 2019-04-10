<?php

namespace jakharbek\chat\forms;

use jakharbek\chat\dto\createChatsDTO;
use jakharbek\chat\factory\ChatsFactory;
use jakharbek\chat\models\Chats;
use Yii;
use yii\base\Model;

/**
 * Class newPublicChat
 * @package jakharbek\chat\forms
 */
class newPublicChat extends Model
{
    public $title = null;
    public $members = [];

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['members'], 'required'],
        ];
    }

    /**
     * @return array|bool|Chats
     * @throws \yii\base\InvalidConfigException
     */
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
        $createChatDTO->type = Chats::TYPE_PUBLIC;
        $createChatDTO->members[] = Yii::$app->user->id;
        $createChatDTO->title = $this->title;
        $chat = $chatFactory::create($createChatDTO);

        if (!($chat instanceof Chats)) {
            $this->addError("user_id", Yii::t("main", "Chat is not created"));
            return false;
        }

        return $chat;
    }
}