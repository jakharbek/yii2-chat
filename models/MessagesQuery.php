<?php

namespace jakharbek\chat\models;

/**
 * This is the ActiveQuery class for [[Messages]].
 *
 * @see Messages
 */
class MessagesQuery extends \yii\db\ActiveQuery
{
    /**
     * @return MessagesQuery
     */
    public function active()
    {
        return $this->status(Messages::STATUS_SENT);
    }
    /**
     * @return MessagesQuery
     */
    public function status($status)
    {
        return $this->andWhere(Messages::tableName().'.[[status]]='.$status);
    }

    /**
     * {@inheritdoc}
     * @return Messages[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Messages|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param $chat_id
     * @return MessagesQuery
     */
    public function chat($chat_id)
    {
        return $this->andWhere([Messages::tableName().'.to_chat_id' => $chat_id]);
    }
}
