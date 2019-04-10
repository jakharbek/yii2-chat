<?php
namespace jakharbek\chat\models;

/**
 * This is the ActiveQuery class for [[ChatsUserUser]].
 *
 * @see ChatsUsers
 */
class ChatsUserUserQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ChatsUserUser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ChatsUserUser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
