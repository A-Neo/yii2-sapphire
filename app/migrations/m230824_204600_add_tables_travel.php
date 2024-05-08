<?php

use yii\db\Migration;

/**
 * Class m230824_204600_add_tables_travel
 */
class m230824_204600_add_tables_travel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('travel_main', [
            'id'         => $this->primaryKey(),
            'id_user'    => $this->integer(11)->notNull()->defaultValue(0),
            'level'      => $this->integer(11)->notNull()->defaultValue(0),
            'created_at' => $this->integer(11)->notNull()->defaultValue(0),
            'updated_at' => $this->integer(11)->notNull()->defaultValue(0),
        ]);

        $tm = time();
        $this->db->createCommand('INSERT INTO travel_main (id_user, level, created_at, updated_at) 
                                                   VALUES (1, 1, ' . $tm . ', ' . $tm . '),
                                                          (1, 2, ' . $tm . ', ' . $tm . '),
                                                          (1, 3, ' . $tm . ', ' . $tm . '),
                                                          (1, 4, ' . $tm . ', ' . $tm . '),
                                                          (1, 5, ' . $tm . ', ' . $tm . '),
                                                          (1, 6, ' . $tm . ', ' . $tm . '),
                                                          (1, 7, ' . $tm . ', ' . $tm . '),
                                                          (1, 8, ' . $tm . ', ' . $tm . ')')->execute();

        $this->createIndex('idx1', 'travel_main', ['id_user']);

        $this->createTable('travel_users', [
            'id'         => $this->primaryKey(),
            'id_table'   => $this->integer(11)->notNull()->defaultValue(0),
            'id_ref'     => $this->integer(11)->notNull()->defaultValue(0),
            'id_user'    => $this->integer(11)->notNull()->defaultValue(0),
            'created_at' => $this->integer(11)->notNull()->defaultValue(0),
            'updated_at' => $this->integer(11)->notNull()->defaultValue(0),
        ]);

        $this->createIndex('idx1', 'travel_users', ['id_table']);
        $this->createIndex('idx2', 'travel_users', ['id_ref', 'id_user']);

        $this->createTable('travel_delay', [
            'id'         => $this->primaryKey(),
            'id_ref'     => $this->integer(11)->notNull()->defaultValue(0),
            'id_user'    => $this->integer(11)->notNull()->defaultValue(0),
            'level'      => $this->integer(11)->notNull()->defaultValue(0),
            'date_end'   => $this->integer(11)->notNull()->defaultValue(0),
        ]);

        $this->createIndex('idx1', 'travel_delay', ['id_ref']);
        $this->createIndex('idx2', 'travel_delay', ['date_end']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('travel_main');
        $this->dropTable('travel_users');
        $this->dropTable('travel_delay');
    }

}
