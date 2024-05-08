<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%payout}}`.
 */
class m220420_191013_create_payout_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%payout}}', [
            'id'         => $this->primaryKey(),
            'balance_id' => $this->bigInteger(),
            'user_id'    => $this->integer(),
            'history_id' => $this->bigInteger(),
            'amount'     => $this->double(),
            'status'     => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey('fk-payout-balance_id', '{{%payout}}', 'balance_id', '{{%balance}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-payout-user_id', '{{%payout}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-payout-history_id', '{{%payout}}', 'history_id', '{{%history}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable('{{%payout}}');
    }
}
