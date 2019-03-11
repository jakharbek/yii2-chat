<?php

use yii\db\Migration;

/**
 * Class m190128_094622_create_table_chat
 */
class m190128_094624_create_table_chats_users_j extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("{{%chats_users}}", [
            'id'      => $this->primaryKey(),
            'chat_id' => $this->integer(),
            'user_id' => $this->integer(),
            'status'  => $this->integer()->defaultValue(1)
        ]);

        $this->addForeignKey("fk-chats_users-chats-chat_id-chat_id", "{{%chats_users}}", 'chat_id', '{{%chats}}', 'chat_id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("{{%chats_users}}");
    }
}
