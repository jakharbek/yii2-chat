<?php

namespace jakharbek\chat\models;

use Yii;

/**
 * This is the model class for table "chats_user_group".
 *
 * @property int $id
 * @property int $chat_id
 * @property int $user_id
 * @property int $status
 *
 * @property Chats $chat
 */
class ChatsUserGroup extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;
    const STATUS_BLOCK = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chats_user_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['chat_id', 'user_id'], 'default', 'value' => null],
            [['status'], 'default','value' => static::STATUS_ACTIVE],
            [['chat_id', 'user_id', 'status'], 'integer'],
            [['chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Chats::className(), 'targetAttribute' => ['chat_id' => 'chat_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'      => 'ID',
            'chat_id' => 'Chat ID',
            'user_id' => 'User ID',
            'status'  => 'Status',
            'title' => 'Title'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChat()
    {
        return $this->hasOne(Chats::className(), ['chat_id' => 'chat_id']);
    }

    /**
     * {@inheritdoc}
     * @return ChatsUserGroupQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ChatsUserGroupQuery(get_called_class());
    }
}
