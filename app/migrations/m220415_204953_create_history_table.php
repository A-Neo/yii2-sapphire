<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%history}}`.
 */
class m220415_204953_create_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%history}}', [
            'id'               => $this->bigPrimaryKey(),
            'user_id'          => $this->integer(),
            'date'             => $this->dateTime(),
            'type'             => $this->string(32),
            'status'           => $this->string(32),
            'from'             => $this->string(32),
            'debitedAmount'    => $this->double(),
            'debitedCurrency'  => $this->string(8),
            'to'               => $this->string(32),
            'creditedAmount'   => $this->double(),
            'creditedCurrency' => $this->string(8),
            'payeerFee'        => $this->double(),
            'gateFee'          => $this->double(),
            'exchangeRate'     => $this->double(),
            'protect'          => $this->string(8),
            'comment'          => $this->string(256),
            'isApi'            => $this->string(8),
        ]);
        $this->addForeignKey('fk-history-user_id', '{{%history}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable('{{%history}}');
    }
}
