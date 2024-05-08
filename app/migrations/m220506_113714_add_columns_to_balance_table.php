<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%balance}}`.
 */
class m220506_113714_add_columns_to_balance_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('{{%balance}}', 'from_sapphire', $this->integer()->defaultValue(0)->after('from_amount'));
        $this->addColumn('{{%balance}}', 'to_sapphire', $this->integer()->defaultValue(0)->after('to_amount'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropColumn('{{%balance}}', 'from_sapphire');
        $this->dropColumn('{{%balance}}', 'to_sapphire');
    }
}
