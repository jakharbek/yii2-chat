<?php
namespace jakharbek\chat\interfaces;

use jakharbek\chat\dto\deleteMessageDTO;
use jakharbek\chat\dto\linkedChatDTO;
use jakharbek\chat\dto\sendMessageChatDTO;
use jakharbek\chat\dto\setSeenMessagesDTO;

/**
 * Interface iChatsServices
 * @package jakharbek\chat\interfaces
 */
interface iChatsServices
{
    /**
     * @param linkedChatDTO $linkedChatDTO
     * @return mixed
     */
    public function linkedChat(linkedChatDTO $linkedChatDTO);

    /**
     * @param sendMessageChatDTO $sendMessageChatDTO
     * @return mixed
     */
    public function sendMessage(sendMessageChatDTO $sendMessageChatDTO);

    /**
     * @param setSeenMessagesDTO $seenMessagesDTO
     * @return mixed
     */
    public function setSeenMessages(setSeenMessagesDTO $seenMessagesDTO);

    /**
     * @param deleteMessageDTO $deleteMessageDTO
     * @return mixed
     */
    public function deleteMessage(deleteMessageDTO $deleteMessageDTO);
}