<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%balance}}`.
 */
class m220526_110843_add_manual_column_to_balance_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('{{%balance}}', 'manual', $this->integer()->notNull()->defaultValue(0)->after('table'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropColumn('{{%balance}}', 'manual');
    }
}
