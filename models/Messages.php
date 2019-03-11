<?php
namespace jakharbek\chat\models;

use Yii;

/**
 * This is the model class for table "messages".
 *
 * @property int $message_id
 * @property string $message
 * @property int $type
 * @property int $replay_message_id
 * @property int $from_user_id
 * @property int $to_chat_id
 * @property int $to_user_id
 * @property int $is_seen
 * @property string $seen
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted_at
 * @property int $isDeleted
 *
 * @property Chats $toChat
 * @property Messages $replayMessage
 * @property Messages[] $messages
 */
class Messages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'messages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['message'], 'string'],
            [['type', 'replay_message_id', 'from_user_id', 'to_chat_id', 'to_user_id', 'is_seen', 'status', 'created_at', 'updated_at', 'deleted_at', 'isDeleted'], 'default', 'value' => null],
            [['type', 'replay_message_id', 'from_user_id', 'to_chat_id', 'to_user_id', 'is_seen', 'status', 'created_at', 'updated_at', 'deleted_at', 'isDeleted'], 'integer'],
            [['seen'], 'string', 'max' => 1024],
            [['to_chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Chats::className(), 'targetAttribute' => ['to_chat_id' => 'chat_id']],
            [['replay_message_id'], 'exist', 'skipOnError' => true, 'targetClass' => Messages::className(), 'targetAttribute' => ['replay_message_id' => 'message_id']],
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
            'to_user_id' => 'To User ID',
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
     * {@inheritdoc}
     * @return MessagesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MessagesQuery(get_called_class());
    }
}
