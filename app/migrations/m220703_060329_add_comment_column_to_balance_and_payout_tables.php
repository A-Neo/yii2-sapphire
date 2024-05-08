<?php

use yii\db\Migration;

/**
 * Class m220703_060329_add_comment_column_to_balance_and_payout_tables
 */
class m220703_060329_add_comment_column_to_balance_and_payout_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%balance}}', 'comment', $this->string(2048)->notNull()->defaultValue('')->after('status'));
        $this->addColumn('{{%payout}}', 'comment', $this->string(2048)->notNull()->defaultValue('')->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%payout}}', 'comment');
        $this->dropColumn('{{%balance}}', 'comment');
    }

}
