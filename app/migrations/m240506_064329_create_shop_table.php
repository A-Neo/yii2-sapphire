<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop}}`.
 */
class m240506_064329_create_shop_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop}}', [
            'id'               => $this->bigPrimaryKey(),
            'name'             => $this->string(255),
            'image'             => $this->string(255),
            'type'             => $this->string(32),
            'status'           => $this->string(32),
            'currency'         => $this->string(64)->notNull()->defaultValue('Payeer'),
            'price'             => $this->double(),
            'comment'          => $this->string(256),
            'created_at'         => $this->integer()->notNull(),
            'updated_at'         => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop}}');
    }
}
