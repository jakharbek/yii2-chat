<?php

namespace jakharbek\chat\api;


use common\components\ApiController;
use jakharbek\chat\dto\getChatsDTO;
use jakharbek\chat\dto\getLastMessageDTO;
use jakharbek\chat\dto\getMessagesDTO;
use jakharbek\chat\forms\deleteMessageForm;
use jakharbek\chat\forms\linkedChatForm;
use jakharbek\chat\forms\newPrivateChat;
use jakharbek\chat\forms\newPublicChat;
use jakharbek\chat\forms\sendMessageForm;
use jakharbek\chat\forms\setSeenMessagesForm;
use jakharbek\chat\forms\unlinkedChatForm;
use jakharbek\chat\interfaces\iChatsRepository;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;

/**
 * Class UserController
 * @package api\modules\v1\controllers
 */
class ChatController extends \yii\rest\Controller
{
    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionGetChats($type, $label)
    {
        /**
         * @var $repository iChatsRepository
         */
        $repository = Yii::createObject(iChatsRepository::class);

        $getChatsDTO = new getChatsDTO();
        $getChatsDTO->user_id = Yii::$app->user->id;
        $getChatsDTO->type = $type;
        $getChatsDTO->label = $label;
        $getChatsDTO->getQuery = true;

        $query = $repository->getChats($getChatsDTO);

        $provider = new ActiveDataProvider([
            'query' => $query
        ]);

        return $provider;
    }

    /**
     * @param $chat_id
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionGetLastMessageChat($chat_id)
    {
        /**
         * @var $repository iChatsRepository
         */
        $repository = Yii::createObject(iChatsRepository::class);

        $getLastMessageDTO = new getLastMessageDTO();
        $getLastMessageDTO->chat_id = $chat_id;

        return $repository->getLastMessage($getLastMessageDTO);
    }

    /**
     * @param $chat_id
     * @param null $forDate
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     */
    public function actionGetMessagesChat($chat_id, $forDate = null)
    {

        /**
         * @var $repository iChatsRepository
         */
        $repository = Yii::createObject(iChatsRepository::class);
        $getMessagesDTO = new getMessagesDTO();
        $getMessagesDTO->chat_id = $chat_id;
        $getMessagesDTO->getQuery = true;
        $getMessagesDTO->forDate = $forDate;
        $query = $repository->getMessages($getMessagesDTO);

        $provider = new ActiveDataProvider([
            'query' => $query
        ]);

        return $provider;
    }

    /**
     * @return array|bool
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSendMessage()
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $form = new sendMessageForm();
        $form->setAttributes($requestParams);

        if (!($message = $form->send())) {
            Yii::$app->response->statusCode = 400;
            Yii::$app->response->statusText = "Bad Request";
            return $form->getErrors();
        }

        return $message;
    }

    /**
     * @return array|bool
     * @throws \yii\base\InvalidConfigException
     */
    public function actionNewPrivateChat()
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $form = new newPrivateChat();
        $form->setAttributes($requestParams);

        if (!($message = $form->create())) {
            Yii::$app->response->statusCode = 400;
            Yii::$app->response->statusText = "Bad Request";
            return $form->getErrors();
        }

        return $message;
    }

    /**
     * @return array|bool
     * @throws \yii\base\InvalidConfigException
     */
    public function actionNewPublicChat()
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $form = new newPublicChat();
        $form->setAttributes($requestParams);

        if (!($message = $form->create())) {
            Yii::$app->response->statusCode = 400;
            Yii::$app->response->statusText = "Bad Request";
            return $form->getErrors();
        }

        return $message;
    }


    /**
     * @return array|bool
     * @throws \yii\base\InvalidConfigException
     */
    public function actionDeleteMessage()
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $form = new deleteMessageForm();
        $form->setAttributes($requestParams);

        if (!($message = $form->delete())) {
            Yii::$app->response->statusCode = 400;
            Yii::$app->response->statusText = "Bad Request";
            return $form->getErrors();
        }

        return $message;
    }

    /**
     * @return array|bool
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSetSeenMessages()
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $form = new setSeenMessagesForm();
        $form->setAttributes($requestParams);

        if (!($message = $form->save())) {
            Yii::$app->response->statusCode = 400;
            Yii::$app->response->statusText = "Bad Request";
            return $form->getErrors();
        }

        return $message;
    }

    public function actionLinkedChat()
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $form = new linkedChatForm();
        $form->setAttributes($requestParams);

        if (!($message = $form->linked())) {
            Yii::$app->response->statusCode = 400;
            Yii::$app->response->statusText = "Bad Request";
            return $form->getErrors();
        }

        return $message;
    }

    public function actionUnlinkedChat()
    {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $form = new unlinkedChatForm();
        $form->setAttributes($requestParams);

        if (!($message = $form->unlinked())) {
            Yii::$app->response->statusCode = 400;
            Yii::$app->response->statusText = "Bad Request";
            return $form->getErrors();
        }

        return $message;
    }


    /**
     * @return array
     */
    public function verbs()
    {
        return ArrayHelper::merge(parent::verbs(), [
            'get-chats' => ['get'],
            'get-last-message-chat' => ['get'],
            'get-messages-chat' => ['get'],
            'send-message' => ['post'],
            'new-private-chat' => ['post'],
            'new-public-chat' => ['post'],
            'delete-message' => ['delete'],
            'set-seen-messages' => ['put'],
            'linked-chat' => ['put'],
            'unlinked-chat' => ['put']
        ]);
    }
}