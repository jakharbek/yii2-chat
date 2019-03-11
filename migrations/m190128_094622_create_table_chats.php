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
            'guid'    => $this->string(),
            'created_at' => $this->integer()->null(),
            'status'     => $this->integer()->defaultValue(1),
            'type'       => $this->integer()
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
