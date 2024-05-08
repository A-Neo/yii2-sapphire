<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tour}}`.
 */
class m220504_182222_create_tour_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%tour}}', [
            'id'         => $this->primaryKey(),
            'user_id'    => $this->integer(),
            'passport'   => 'LONGBLOB',
            'number'     => $this->string()->notNull(),
            'whatsapp'     => $this->string()->notNull(),
            'status'     => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey('fk-tour-user_id', '{{%tour}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable('{{%tour}}');
    }
}
