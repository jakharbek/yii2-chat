<?php
namespace jakharbek\chat\models;

/**
 * This is the ActiveQuery class for [[ChatsUsers]].
 *
 * @see ChatsUsers
 */
class ChatsUsersQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ChatsUsers[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ChatsUsers|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
