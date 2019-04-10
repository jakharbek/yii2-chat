<?php

namespace jakharbek\chat\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "chats".
 *
 * @property int $chat_id
 * @property int $created_at
 * @property int $status
 * @property int $type
 * @property string $title
 * @property int $owner_id
 * @property string $label
 * @property-read bool $isPublic
 * @property-read bool $isPrivate
 *
 * @property Messages[] $messages
 * @property ChatsUserUser $chatsUserUser
 * @property ChatsUserGroup[] $chatsUserGroup
 */
class Chats extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const TYPE_PRIVATE = 1;
    const TYPE_PUBLIC = 2;


    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false,

            ],
        ];
    }

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
            [['created_at', 'status', 'type', 'title', 'owner_id', 'label'], 'default', 'value' => null],
            [['created_at', 'status', 'type', 'owner_id'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'chat_id' => 'Chat ID',
            'title' => 'Title',
            'created_at' => 'Created At',
            'status' => 'Status',
            'type' => 'Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChatsUserGroup()
    {
        return $this->hasMany(ChatsUserGroup::className(), ['chat_id' => 'chat_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChatsUserUser()
    {
        return $this->hasOne(ChatsUserUser::className(), ['chat_id' => 'chat_id']);
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

    /**
     * @return bool
     */
    public function getIsPublic()
    {
        return $this->type == self::TYPE_PUBLIC;
    }

    /**
     * @return bool
     */
    public function getIsPrivate()
    {
        return $this->type == self::TYPE_PRIVATE;
    }

    /**
     * @param $user_id
     * @return bool
     */
    public function isOwner($user_id)
    {
        return $user_id == $this->owner_id;
    }

}
