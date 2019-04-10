<?php
namespace jakharbek\chat\dto;

use jakharbek\chat\models\Messages;

class getMessagesDTO
{
    public $chat_id;
    public $status = Messages::STATUS_SENT;
    public $getQuery = false;
    public $forDate = null;
}