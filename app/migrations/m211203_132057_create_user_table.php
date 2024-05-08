<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m211203_132057_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $tableOptions = null;
        if($this->db->driverName === 'mysql'){
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%user}}', [
            'id'                   => $this->primaryKey(),
            'username'             => $this->string()->notNull()->unique(),
            'auth_key'             => $this->string(32)->notNull(),
            'password_hash'        => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email'                => $this->string()->notNull()->unique(),
            'verification_token'   => $this->string()->defaultValue(null),
            'role'                 => $this->string(32)->notNull()->defaultValue('user'),
            'permissions'          => $this->json()->notNull(),
            'referrer_id'          => $this->integer(),
            'wallet'               => $this->string(),
            'sapphire'             => $this->integer()->defaultValue(0)->notNull(),
            'balance'              => $this->decimal(20, 8)->defaultValue(0)->notNull(),
            'accumulation'         => $this->decimal(20, 8)->defaultValue(0)->notNull(),
            'full_name'            => $this->string(1024),
            'country'              => $this->string(256),
            'phone'                => $this->string(256),
            'birth_date'           => $this->date(),
            'avatar'               => 'LONGBLOB',
            'status'               => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at'           => $this->integer()->notNull(),
            'updated_at'           => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addForeignKey('fk-user-referrer_id', '{{%user}}', 'referrer_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $now = time();
        $this->batchInsert('{{%user}}', ['username', 'email', 'password_hash', 'auth_key', 'role', 'permissions', 'created_at', 'updated_at'], [
            ['admin', 'admin@admin.com', Yii::$app->security->generatePasswordHash('Pass314!'), Yii::$app->security->generateRandomString(), 'admin', '[]', $now, $now],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable('{{%user}}');
    }
}
