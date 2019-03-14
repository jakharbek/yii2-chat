<?php

use yii\db\Migration;

/**
 * Class m190128_094622_create_table_chat
 */
class m190128_094623_create_table_messages extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable("{{%messages}}", [
            'message_id'        => $this->primaryKey(),
            'message'           => $this->text(),
            'type'              => $this->integer(),
            'replay_message_id' => $this->integer()->null(),
            'from_user_id'      => $this->integer(),
            'to_chat_id'        => $this->integer(),
            'to_user_id'        => $this->integer()->null(),
            'is_seen'           => $this->integer()->defaultValue(0),
            'seen'              => $this->string(1024),
            'from_status'            => $this->integer()->defaultValue(0),
            'to_status'            => $this->integer()->defaultValue(0),
            'created_at'        => $this->integer(),
            'updated_at'        => $this->integer()->defaultValue(0),
            'deleted_at'        => $this->integer()->defaultValue(0),
            'isDeleted'         => $this->integer()->defaultValue(0),
        ]);

        $this->addForeignKey("fk-message-chat-to_chat_id-chat_id", "{{%messages}}", 'to_chat_id', '{{%chats}}', 'chat_id', 'CASCADE');
        $this->addForeignKey("fk-message-chat-replay_message_id-message_id", "{{%messages}}", 'replay_message_id', '{{%messages}}', 'message_id', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable("{{%messages}}");
    }
}
