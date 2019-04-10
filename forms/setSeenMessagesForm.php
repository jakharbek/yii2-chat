<?php

namespace jakharbek\chat\forms;

use jakharbek\chat\dto\setSeenMessagesDTO;
use jakharbek\chat\interfaces\iChatsServices;
use jakharbek\chat\services\ChatServices;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class setSeenMessagesForm
 * @package common\modules\chat\forms
 */
class setSeenMessagesForm extends Model
{
    public $messages_id_explode;

    /**
     * @return array
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['messages_id_explode'], 'required'],
        ]);
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        try {
            $ids = explode(",", $this->messages_id_explode);
            /**
             * @var $service iChatsServices
             */
            $service = Yii::createObject(['class' => iChatsServices::class]);
            $setSeenMessagesDTO = new setSeenMessagesDTO();
            $setSeenMessagesDTO->user_id = Yii::$app->user->id;
            $setSeenMessagesDTO->message_ids = $ids;
            $messages = $service->setSeenMessagesById($setSeenMessagesDTO);


        } catch (\Exception $exception) {
            $this->addError("message_id", $exception->getMessage());
            return false;
        }
        return $messages;
    }
}