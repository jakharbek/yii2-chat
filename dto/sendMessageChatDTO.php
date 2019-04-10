<?php
namespace jakharbek\chat\dto;

use jakharbek\chat\models\Messages;

class sendMessageChatDTO
{
    public $chat_id;
    public $message;
    public $replay_message_id;
    public $from_user_id;
    public $type = Messages::TYPE_USUALLY;
}