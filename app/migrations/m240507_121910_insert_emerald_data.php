<?php

use yii\db\Migration;

/**
 * Class m240507_121910_insert_emerald_data
 */
class m240507_121910_insert_emerald_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        for ($i = 2; $i < 5; $i++) { // Замените 100 на количество записей, которые вы хотите добавить
//            $this->insert('emerald_main', [
//                'id_user' => 10,
//                'status' => 1,
//                'level' => 1,
//            ]);
            $this->insert('emerald_users', [
                'id_table' => 3,
                'id_ref' => 10,
                'id_user' => $i,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        for ($i = 2; $i < 5; $i++) { // Замените 100 на количество записей, которые вы хотите добавить
//            $this->delete('emerald_main', [
//                'id_user' => 10,
//                'status' => 1,
//                'level' => 1,
//            ]);
            $this->delete('emerald_users', [
                'id_table' => 3,
                'id_ref' => 10,
                'id_user' => $i,
            ]);
        }
        //return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240507_121910_insert_emerald_data cannot be reverted.\n";

        return false;
    }
    */
}
