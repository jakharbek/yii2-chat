<?php

namespace jakharbek\chat\interfaces;

use jakharbek\chat\dto\getChatDTO;
use jakharbek\chat\dto\getChatsDTO;
use jakharbek\chat\dto\getLastMessageDTO;
use jakharbek\chat\dto\getMessagesDTO;
use jakharbek\chat\models\Chats;

/**
 * Interface iChatsRepository
 * @package jakharbek\chat\interfaces
 */
interface iChatsRepository
{
    /**
     * @param $chat_id
     * @return Chats|null
     */
    public function getChatById($chat_id): ?Chats;

    /**
     * @param $message_id
     * @return Messages|null
     */
    public function getMessageById($message_id): ?Messages;

    /**
     * @param getChatsDTO $getChatsDTO
     * @return mixed
     */
    public function getChats(getChatsDTO $getChatsDTO);

    /**
     * @param getLastMessageDTO $getLastMessageDTO
     * @return mixed
     */
    public function getLastMessage(getLastMessageDTO $getLastMessageDTO);

    /**
     * @param getMessagesDTO $getMessagesDTO
     * @return mixed
     */
    public function getMessages(getMessagesDTO $getMessagesDTO);

}