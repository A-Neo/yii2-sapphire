<?php

use yii\db\Migration;

/**
 * Class m240504_235601_create_emerald_tabel
 */
class m240504_235601_create_emerald_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%emerald_main}}', [
            'id'         => $this->primaryKey(),
            'id_user'    => $this->integer(11)->notNull()->defaultValue(0),
            'level'      => $this->integer(2)->notNull()->defaultValue(0),
            'status'     => $this->integer(2)->notNull()->defaultValue(1),
            'created_at' => $this->integer(11)->notNull()->defaultValue(0),
            'updated_at' => $this->integer(11)->notNull()->defaultValue(0),
        ]);
        $this->createIndex('idx1', '{{%emerald_main}}', ['id_user']);

        $this->createTable('{{%emerald_users}}', [
            'id'         => $this->primaryKey(),
            'id_table'   => $this->integer(11)->notNull()->defaultValue(0),
            'id_ref'     => $this->integer(11)->notNull()->defaultValue(0),
            'id_user'    => $this->integer(11)->notNull()->defaultValue(0),
            'created_at' => $this->integer(11)->notNull()->defaultValue(0),
            'updated_at' => $this->integer(11)->notNull()->defaultValue(0),
        ]);

        // it-table - это таблица (стол) уровень на котором находятся id_user от id_ref
        $this->createIndex('idx1', '{{%emerald_users}}', ['id_table']);
        $this->createIndex('idx2', '{{%emerald_users}}', ['id_ref', 'id_user']);

        $this->createTable('{{%emerald_delay}}', [
            'id'         => $this->primaryKey(),
            'id_ref'     => $this->integer(11)->notNull()->defaultValue(0),
            'id_user'    => $this->integer(11)->notNull()->defaultValue(0),
            'level'      => $this->integer(11)->notNull()->defaultValue(0),
            'date_end'   => $this->integer(11)->notNull()->defaultValue(0),
        ]);

        $this->createIndex('idx1', '{{%emerald_delay}}', ['id_ref']);
        $this->createIndex('idx2', '{{%emerald_delay}}', ['date_end']);


        $this->createTable('{{%emerald_history}}', [
            'id'               => $this->bigPrimaryKey(),
            'user_id'          => $this->integer(),
            'date'             => $this->dateTime(),
            'type'             => $this->string(32),
            'status'           => $this->string(32),
            'currency'         => $this->string(64)->notNull()->defaultValue('Payeer'),
            'from'             => $this->string(32),
            'debitedAmount'    => $this->double(),
            'debitedCurrency'  => $this->string(8),
            'to'               => $this->string(32),
            'creditedAmount'   => $this->double(),
            'creditedCurrency' => $this->string(8),
            'payeerFee'        => $this->double(),
            'gateFee'          => $this->double(),
            'exchangeRate'     => $this->double(),
            'protect'          => $this->string(8),
            'comment'          => $this->string(256),
            'isApi'            => $this->string(8),
        ]);
        $this->addForeignKey('fk-emerald_history-user_id', '{{%emerald_history}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%emerald_balance}}', [
            'id'                 => $this->bigPrimaryKey(),
            'user_id'            => $this->integer(),
            'payment'            => $this->double(),
            'month'              => $this->integer(),
            'slot'               => $this->integer(),
            'start_at'           => $this->integer(),
            'end_at'             => $this->integer(),
            'bonus'              => $this->string(255),
            'status'             => $this->integer(),
            'created_at'         => $this->integer()->notNull(),
            'updated_at'         => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk-emerald_balance-user_id', '{{%emerald_balance}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%emerald_order}}', [
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
        $this->createIndex('idx1', '{{%emerald_order}}', ['id_user']);

        $this->addColumn('{{%user}}', 'id_ref_emerald',$this->integer(2)->null()->defaultValue(0)->after('balance_travel'));
        $this->addColumn('{{%user}}', 'activation_emerald',$this->integer(2)->notNull()->defaultValue(0)->after('id_ref_emerald'));
        $this->addColumn('{{%user}}', 'balance_emerald', $this->decimal(20, 8)->defaultValue(0)->notNull()->after('activation_emerald'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%emerald_main}}');
        $this->dropTable('{{%emerald_users}}');
        $this->dropTable('{{%emerald_delay}}');

        $this->dropTable('{{%emerald_balance}}');
        $this->dropTable('{{%emerald_history}}');

        $this->dropTable('{{%emerald_order}}');

        $this->dropColumn('user', 'activation_emerald');
        $this->dropColumn('user', 'balance_emerald');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240504_235601_create_emerald_tabel cannot be reverted.\n";

        return false;
    }
    */
}
