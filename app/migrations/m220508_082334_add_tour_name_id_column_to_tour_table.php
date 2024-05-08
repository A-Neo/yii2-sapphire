<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%tour}}`.
 */
class m220508_082334_add_tour_name_id_column_to_tour_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->truncateTable('{{%tour}}');
        $this->addColumn('{{%tour}}', 'tour_name_id', $this->integer()->notNull()->after('user_id'));
        $this->addForeignKey('fk-tour-tour_name_id', '{{%tour}}', 'tour_name_id', '{{%tour_name}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropForeignKey('fk-tour-tour_name_id', '{{%tour}}');
        $this->dropColumn('{{%tour}}', 'tour_name_id');
    }
}
