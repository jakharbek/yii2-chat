<?php
namespace jakharbek\chat\models;

/**
 * This is the ActiveQuery class for [[ChatsUserGroup]].
 *
 * @see ChatsUsers
 */
class ChatsUserGroupQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ChatsUserGroup[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ChatsUserGroup|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
