<?php

use yii\db\Migration;

/**
 * Handles the creation of table `news`.
 */
class m211203_220000_create_news_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $tableOptions = null;
        if($this->db->driverName === 'mysql'){
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%news}}', [
            'id'              => $this->primaryKey(),
            'title'           => $this->string(2048)->notNull(),
            'seo_title'       => $this->string(2048)->notNull(),
            'seo_description' => $this->string(2048)->notNull(),
            'seo_keywords'    => $this->string(2048)->notNull(),
            'short'           => $this->string(2048)->notNull(),
            'text'            => $this->text()->notNull(),
            'status'          => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at'      => $this->integer()->notNull(),
            'updated_at'      => $this->integer()->notNull(),
            'published_at'    => $this->integer()->notNull(),
        ], $tableOptions);
        $this->createIndex('idx-news-published_at', '{{%news}}', 'published_at');
        $now = time();
        $this->batchInsert('{{%news}}', ['title', 'seo_title', 'seo_description', 'seo_keywords', 'short', 'text', 'created_at', 'updated_at', 'published_at'], [
            ['Пример новости', 'Пример новости', 'Пример новости', 'Пример новости', 'Краткий текст новости. Отображается только в списке.', 'Полный текст новости. Отображается на отдельной странице.', $now, $now, $now],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable('{{%news}}');
    }
}
