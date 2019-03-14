<?php
namespace jakharbek\chat\dto;

use jakharbek\chat\models\Messages;

class createMessagesDTO
{
    public $message;
    public $type = Messages::TYPE_USUALLY;
    public $replay_message_id = null;
    public $from_user_id;
    public $to_chat_id;
    public $to_user_id = null;
}