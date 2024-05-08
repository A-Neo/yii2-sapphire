<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%history}}`.
 */
class m220703_105646_add_currency_column_to_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('{{%history}}', 'currency', $this->string(64)->notNull()->defaultValue('Payeer')->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropColumn('{{%history}}', 'currency');
    }
}
