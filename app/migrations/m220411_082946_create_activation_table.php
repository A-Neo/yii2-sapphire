<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%activation}}`.
 */
class m220411_082946_create_activation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable('{{%activation}}', [
            'id'         => $this->primaryKey(),
            'user_id'    => $this->integer()->notNull(),
            'table'      => $this->integer()->notNull()->defaultValue(1),
            'status'     => $this->integer()->notNull()->defaultValue(0),// 0 новый, 1-оплачен
            'clone'      => $this->integer()->notNull()->defaultValue(0), // 0 - первое место 1 и больше клоны (Реинвест сюда или отдельной колонкой ?)
            'start'      => $this->integer()->notNull()->defaultValue(1), // стартовый стол
            't1_left'    => $this->integer(),
            't1_right'   => $this->integer(),
            't2_left'    => $this->integer(),
            't2_right'   => $this->integer(),
            't3_left'    => $this->integer(),
            't3_right'   => $this->integer(),
            't4_left'    => $this->integer(),
            't4_right'   => $this->integer(),
            't5_left'    => $this->integer(),
            't5_right'   => $this->integer(),
            't6_left'    => $this->integer(),
            't6_right'   => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey('fk-activation-user_id', '{{%activation}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable('{{%activation}}');
    }
}
