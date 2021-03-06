<?php

use yii\db\Migration;

/**
 * Class m190128_094624_create_table_chats_user_user_j
 */
class m190128_094625_create_table_chats_user_group_j extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("{{%chats_user_group}}", [
            'id'      => $this->primaryKey(),
            'chat_id' => $this->integer(),
            'user_id' => $this->integer(),
            'status'  => $this->integer()->defaultValue(1)
        ]);

        $this->addForeignKey("fk-chats_user_group-chats-chat_id-chat_id", "{{%chats_user_group}}", 'chat_id', '{{%chats}}', 'chat_id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("{{%chats_user_group}}");
    }
}
