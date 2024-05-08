<?php

use yii\db\Migration;

/**
 * Handles the creation of table `page`.
 */
class m211203_210000_create_page_tables extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $tableOptions = null;
        if($this->db->driverName === 'mysql'){
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%page}}', [
            'id'              => $this->primaryKey(),
            'slug'            => $this->string()->notNull()->unique(),
            'title'           => $this->string(2048)->notNull(),
            'seo_title'       => $this->string(2048)->notNull(),
            'seo_description' => $this->string(2048)->notNull(),
            'seo_keywords'    => $this->string(2048)->notNull(),
            'text'            => $this->text()->notNull(),
            'status'          => $this->smallInteger()->notNull()->defaultValue(1),
            'position'        => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at'      => $this->integer()->notNull(),
            'updated_at'      => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createIndex('idx-page-slug', '{{%page}}', 'slug');
        $now = time();
        $this->batchInsert('{{%page}}', ['slug', 'position', 'title', 'seo_title', 'seo_description', 'seo_keywords', 'text', 'created_at', 'updated_at'], [
            ['index', 1, 'Главная', 'Главная', 'Главная', 'Главная', 'Текст на главной', $now, $now],
            ['about', 2, 'О нас', 'О нас', 'О нас', 'О нас', 'Информация о нас', $now, $now],
            // ['contact', 3, 'Контакты', 'Контакты', 'Контактыс', 'Контакты', 'Контактная информация', $now, $now],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable('{{%page}}');
    }
}
