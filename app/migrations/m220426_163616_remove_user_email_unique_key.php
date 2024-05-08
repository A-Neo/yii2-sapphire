<?php

use yii\db\Migration;

/**
 * Class m220426_163616_remove_user_email_unique_key
 */
class m220426_163616_remove_user_email_unique_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->dropIndex('email', '{{%user}}');
        $this->createIndex('email', '{{%user}}', 'email');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropIndex('email', '{{%user}}');
        $this->createIndex('email', '{{%user}}', 'email', true);
    }

}
