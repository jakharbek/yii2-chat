<?php

namespace jakharbek\chat\models;

use common\models\User;
use jakharbek\chat\dto\deleteMessageDTO;
use jakharbek\chat\exceptions\ChatException;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "messages".
 *
 * @property int $message_id
 * @property string $message
 * @property int $type
 * @property int $replay_message_id
 * @property int $from_user_id
 * @property int $to_chat_id
 * @property int $is_seen
 * @property string $seen
 * @property int $to_status
 * @property int $from_status
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted_at
 * @property int $isDeleted
 * @property int $receiverId
 *
 * @property Chats $toChat
 * @property Messages $replayMessage
 * @property Messages[] $messages
 */
class Messages extends \yii\db\ActiveRecord
{
    const TYPE_USUALLY = 1;
    const STATUS_DELETED = 0;
    const STATUS_SENT = 1;

    const DELETE_FOR_ALL = 1;
    const DELETE_FOR_SENDER = 2;
    const DELETE_FOR_RECEIVER = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'messages';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
            ],

            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true,
                    'deleted_at' => time(),
                ],
                'replaceRegularDelete' => true // mutate native `delete()` method
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['message'], 'string'],
            [['type', 'replay_message_id', 'from_user_id', 'to_chat_id', 'is_seen', 'created_at', 'updated_at', 'deleted_at', 'isDeleted'], 'default', 'value' => null],
            [['type', 'replay_message_id', 'from_user_id', 'to_chat_id', 'is_seen', 'to_status', 'from_status', 'created_at', 'updated_at', 'deleted_at', 'isDeleted'], 'integer'],
            [['seen'], 'string', 'max' => 1024],
            [['to_chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Chats::className(), 'targetAttribute' => ['to_chat_id' => 'chat_id']],
            [['replay_message_id'], 'exist', 'skipOnError' => true, 'targetClass' => Messages::className(), 'targetAttribute' => ['replay_message_id' => 'message_id']],
            [['type', 'is_seen', 'created_at', 'updated_at', 'deleted_at', 'isDeleted'], 'default', 'value' => 0],
            [['to_status', 'from_status'], 'default', 'value' => static::STATUS_SENT]

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'message_id' => 'Message ID',
            'message' => 'Message',
            'type' => 'Type',
            'replay_message_id' => 'Replay Message ID',
            'from_user_id' => 'From User ID',
            'to_chat_id' => 'To Chat ID',
            'is_seen' => 'Is Seen',
            'seen' => 'Seen',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'isDeleted' => 'Is Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getToChat()
    {
        return $this->hasOne(Chats::className(), ['chat_id' => 'to_chat_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReplayMessage()
    {
        return $this->hasOne(Messages::className(), ['message_id' => 'replay_message_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Messages::className(), ['replay_message_id' => 'message_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'from_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return MessagesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MessagesQuery(get_called_class());
    }

    /**
     * @param null $user_id
     */
    public function setSeen($user_id = null)
    {
        $this->updateAttributes(['is_seen' => true]);
        if ($user_id !== null):
            $this->addToSeenUser($user_id);
        endif;
    }

    /**
     * @param $user_id
     */
    public function addToSeenUser($user_id)
    {
        $seen = @Json::decode($this->seen);
        if (is_array($seen)):
            if (array_key_exists($user_id, $seen)) {
                return;
            }
        endif;;
        $seen[$user_id] = time();
        $seen = Json::encode($seen);
        $this->updateAttributes(['seen' => $seen]);
    }

    public function isOwner($user_id)
    {
        if ($this->from_user_id == $user_id) {
            return true;
        }
        return false;
    }

    /**
     * @param deleteMessageDTO $deleteMessageDTO
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deleteForAll(deleteMessageDTO $deleteMessageDTO)
    {
        $this->deleteForSender($deleteMessageDTO);
        $this->deleteForReceiver($deleteMessageDTO);
        $this->delete();
    }

    /**
     * @param deleteMessageDTO $deleteMessageDTO
     */
    public function deleteForSender(deleteMessageDTO $deleteMessageDTO)
    {
        $chat = $this->toChat;

        if (!$this->canDeleteSender($deleteMessageDTO->user_id)) {
            throw new ChatException("Message can not be deleted");
        }

        $this->updateAttributes(['from_status' => Messages::STATUS_DELETED]);
    }

    /**
     * @param deleteMessageDTO $deleteMessageDTO
     */
    public function deleteForReceiver(deleteMessageDTO $deleteMessageDTO)
    {

        $chat = $this->toChat;

        if (!$this->canDeleteReceiver($deleteMessageDTO->user_id)) {
            throw new ChatException("Message can not be deleted");
        }

        $this->updateAttributes(['to_status' => Messages::STATUS_DELETED]);
    }

    /**
     * @return int
     * @throws ChatException
     */
    public function getReceiverId()
    {
        $chat = $this->toChat;

        if (!$chat->isPrivate) {
            throw new ChatException("Chat is not private for get receiverId");
        }

        $chatsUserUser = $chat->chatsUserUser;

        return ($chatsUserUser->user_id_1 == $this->from_user_id) ? $chatsUserUser->user_id_2 : $chatsUserUser->user_id_1;
    }

    /**
     * @param $user_id
     * @return bool
     */
    public function canDeleteSender($user_id)
    {
        $chat = $this->toChat;
        return $this->isSender($user_id) || $chat->isOwner($user_id);
    }

    /**
     * @param $user_id
     * @return bool
     */
    public function canDeleteReceiver($user_id)
    {
        $chat = $this->toChat;

        if (!$chat->isPrivate) {
            return $this->canDeleteSender($user_id);
        }

        $chat = $this->toChat;
        return $this->canDeleteSender($user_id) || $this->isReceiver($user_id);
    }

    /**
     * @param $user_id
     * @return bool
     */
    public function isReceiver($user_id)
    {
        return $user_id == $this->receiverId;
    }

    /**
     * @param $user_id
     * @return bool
     */
    public function isSender($user_id)
    {
        return $user_id == $this->from_user_id;
    }

    public function fields()
    {
        return ArrayHelper::merge(parent::fields(), [
            'user'
        ]); // TODO: Change the autogenerated stub
    }

}
