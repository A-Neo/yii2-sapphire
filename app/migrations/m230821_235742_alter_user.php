<?php

use yii\db\Migration;

/**
 * Class m230821_235742_alter_user
 */
class m230821_235742_alter_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'balance_travel',
                         $this->decimal(20, 8)->defaultValue(0)->notNull()->after('balance'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'balance_travel');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230821_235742_alter_user cannot be reverted.\n";

        return false;
    }
    */
}
