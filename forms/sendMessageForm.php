<?php

namespace jakharbek\chat\forms;

use jakharbek\chat\dto\sendMessageChatDTO;
use jakharbek\chat\services\ChatServices;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class sendMessageForm
 * @package common\modules\chat\forms
 */
class sendMessageForm extends Model
{
    public $message;
    public $chat_id;
    public $replay_message_id;

    /**
     * @return array
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['message', 'chat_id'], 'required'],
            [['message'], 'string'],
            [['replay_message_id'], 'safe'],
        ]);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function send()
    {
        if (!$this->validate()) {
            return false;
        }
        try {

            $service = Yii::createObject(['class' => ChatServices::class]);
            $sendMessageChatDTO = new sendMessageChatDTO();
            $sendMessageChatDTO->from_user_id = Yii::$app->user->id;
            $sendMessageChatDTO->message = $this->message;
            $sendMessageChatDTO->chat_id = $this->chat_id;
            $sendMessageChatDTO->replay_message_id = $this->replay_message_id;
            $messageObject = $service->sendMessage($sendMessageChatDTO);

            try {
                Yii::$app->user->identity->reCalculateResponseTime();
            } catch (\Exception $exception) {
            }


        } catch (\Exception $exception) {
            $this->addError("message", $exception->getMessage());
            return false;
        }
        return $messageObject;
    }
}