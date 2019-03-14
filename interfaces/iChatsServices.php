<?php
namespace jakharbek\chat\interfaces;

use jakharbek\chat\dto\sendMessageChatDTO;

interface iChatsServices
{
    public function sendMessage(sendMessageChatDTO $sendMessageChatDTO);

}