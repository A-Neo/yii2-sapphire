<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tour_name}}`.
 */
class m220508_082301_create_tour_name_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%tour_name}}', [
            'id'         => $this->primaryKey(),
            'price'      => $this->integer()->notNull()->defaultValue(3),
            'name'       => $this->string(2048)->notNull(),
            'status'     => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable('{{%tour_name}}');
    }
}
