<?php

use yii\db\Migration;

/**
 * Class m190128_094622_create_table_chat
 */
class m190128_094622_create_table_chats extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("{{%chats}}", [
            'chat_id' => $this->primaryKey(),
            'created_at' => $this->integer()->null(),
            'status'     => $this->integer()->defaultValue(1),
            'type'       => $this->integer(),
            'title' => $this->string(),
            'owner_id' => $this->integer(),
            'label' => $this->string()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("{{%chats}}");
    }
}
