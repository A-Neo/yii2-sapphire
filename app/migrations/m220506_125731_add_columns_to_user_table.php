<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user}}`.
 */
class m220506_125731_add_columns_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('{{%user}}', 'sapphire_personal', $this->integer()->defaultValue(0)->after('sapphire'));
        $this->addColumn('{{%user}}', 'sapphire_partners', $this->integer()->defaultValue(0)->after('sapphire_personal'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropColumn('{{%user}}', 'sapphire_personal');
        $this->dropColumn('{{%user}}', 'sapphire_partners');
    }
}
