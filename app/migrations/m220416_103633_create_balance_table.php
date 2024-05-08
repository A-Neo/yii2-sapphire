<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%balance}}`.
 */
class m220416_103633_create_balance_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%balance}}', [
            'id'                 => $this->bigPrimaryKey(),
            'type'               => $this->integer(),
            'table'              => $this->integer(),
            'from_activation_id' => $this->integer(),
            'to_activation_id'   => $this->integer(),
            'from_user_id'       => $this->integer(),
            'to_user_id'         => $this->integer(),
            'history_id'         => $this->bigInteger(),
            'from_amount'        => $this->double(),
            'to_amount'          => $this->double(),
            'status'             => $this->integer()->notNull()->defaultValue(0),
            'created_at'         => $this->integer()->notNull(),
            'updated_at'         => $this->integer()->notNull(),
        ]);
        $this->addForeignKey('fk-balance-from_activation_id', '{{%balance}}', 'from_activation_id', '{{%activation}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-balance-to_activation_id', '{{%balance}}', 'to_activation_id', '{{%activation}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-balance-from_user_id', '{{%balance}}', 'from_user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-balance-to_user_id', '{{%balance}}', 'to_user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-balance-history_id', '{{%balance}}', 'history_id', '{{%history}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable('{{%balance}}');
    }
}
