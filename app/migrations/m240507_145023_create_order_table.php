<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order}}`.
 */
class m240507_145023_create_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order}}', [
            'id'         => $this->primaryKey(),
            'id_user'    => $this->integer(11)->notNull()->defaultValue(0),
            'order'      => $this->string(255)->null()->defaultValue(null),
            'product_id' => $this->integer(11)->notNull()->defaultValue(0),
            'fullname'   => $this->string(255)->null()->defaultValue(null),
            'country'    => $this->string(256)->null()->defaultValue(null),
            'city'       => $this->string(256)->null()->defaultValue(null),
            'zip_code'   => $this->string(256)->null()->defaultValue(null),
            'phone'      => $this->string(256)->null()->defaultValue(null),
            'whatsapp'   => $this->string(256)->null()->defaultValue(null),
            'status'     => $this->integer(2)->notNull()->defaultValue(1),
            'created_at' => $this->integer(11)->notNull()->defaultValue(0),
            'updated_at' => $this->integer(11)->notNull()->defaultValue(0),
        ]);

        $this->createIndex('idx1', '{{%order}}', ['id_user']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%order}}');
    }
}
