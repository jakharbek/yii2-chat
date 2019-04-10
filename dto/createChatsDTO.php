<?php
namespace jakharbek\chat\dto;

use jakharbek\chat\models\Chats;

/**
 * Class createChatsDTO
 * @package jakharbek\chat\dto
 */
class createChatsDTO
{
    public $title = null;
    public $status = Chats::STATUS_ACTIVE;
    public $type = Chats::TYPE_PRIVATE;
    public $owner_id = null;
    public $members = [];
}