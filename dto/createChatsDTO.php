<?php

namespace jakharbek\chat\dto;

use jakharbek\chat\models\Chats;

class createChatsDTO
{
    public $diolog_1 = "";
    public $diolog_2 = "";
    public $diolog_type = "private";
    public $status = Chats::STATUS_ACTIVE;
    public $type = Chats::TYPE_PRIVATE;
}