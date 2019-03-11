<?php
namespace jakharbek\chat\models;

use Yii;

/**
 * This is the model class for table "chats".
 *
 * @property int $chat_id
 * @property string $guid
 * @property int $created_at
 * @property int $status
 * @property int $type
 *
 * @property ChatsUsers[] $chatsUsers
 * @property Messages[] $messages
 */
class Chats extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chats';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'status', 'type'], 'default', 'value' => null],
            [['created_at', 'status', 'type'], 'integer'],
            [['guid'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'chat_id' => 'Chat ID',
            'guid' => 'Guid',
            'created_at' => 'Created At',
            'status' => 'Status',
            'type' => 'Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChatsUsers()
    {
        return $this->hasMany(ChatsUsers::className(), ['chat_id' => 'chat_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Messages::className(), ['to_chat_id' => 'chat_id']);
    }

    /**
     * {@inheritdoc}
     * @return ChatsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ChatsQuery(get_called_class());
    }
}
