<?php

use app\models\enumerables\SettingType;
use yii\db\Migration;

/**
 * Class m211206212121_create_setting_table
 */
class m211206_212121_create_setting_table extends Migration
{
    /**
     * This method contains the logic to be executed when applying this migration.
     */
    public function up() {
        $tableOptions = null;
        if($this->db->driverName === 'mysql'){
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%setting}}', [
            'id'          => $this->primaryKey(),
            'type'        => $this->string(10)->notNull(),
            'section'     => $this->string()->notNull(),
            'key'         => $this->string()->notNull(),
            'value'       => $this->text()->notNull(),
            'status'      => $this->smallInteger()->notNull()->defaultValue(1),
            'description' => $this->string(),
            'system'      => $this->boolean()->notNull()->defaultValue(0),
            'created_at'  => $this->integer()->notNull(),
            'updated_at'  => $this->integer()->notNull(),
        ], $tableOptions);
        $this->batchInsert('{{%setting}}', ['type', 'section', 'key', 'value', 'system', 'created_at', 'updated_at'], [
            [SettingType::STRING_TYPE, 'system', 'SiteName', 'Sapphire Group', 1, 0, 0],
            [SettingType::STRING_TYPE, 'news', 'SeoTitle', 'News', 1, 0, 0],
            [SettingType::STRING_TYPE, 'news', 'SeoDescription', 'News', 1, 0, 0],
            [SettingType::STRING_TYPE, 'news', 'SeoKeywords', 'News', 1, 0, 0],
            [SettingType::INTEGER_TYPE, 'system', 'activationAmount', '12', 1, 0, 0],
            [SettingType::INTEGER_TYPE, 'system', 'transferFee', '1', 1, 0, 0],
            [SettingType::INTEGER_TYPE, 'system', 'payoutFee', '2', 1, 0, 0],
            [SettingType::INTEGER_TYPE, 'system', 'chargingAmount1', '10', 1, 0, 0],
            [SettingType::INTEGER_TYPE, 'system', 'promotionAmount1', '10', 1, 0, 0],
            [SettingType::INTEGER_TYPE, 'system', 'chargingAmount2', '30', 1, 0, 0],
            [SettingType::INTEGER_TYPE, 'system', 'promotionAmount2', '30', 1, 0, 0],
            [SettingType::INTEGER_TYPE, 'system', 'chargingAmount3', '90', 1, 0, 0],
            [SettingType::INTEGER_TYPE, 'system', 'promotionAmount3', '90', 1, 0, 0],
            [SettingType::INTEGER_TYPE, 'system', 'chargingAmount4', '270', 1, 0, 0],
            [SettingType::INTEGER_TYPE, 'system', 'promotionAmount4', '270', 1, 0, 0],
            [SettingType::INTEGER_TYPE, 'system', 'chargingAmount5', '810', 1, 0, 0],
            [SettingType::INTEGER_TYPE, 'system', 'promotionAmount5', '810', 1, 0, 0],
            [SettingType::INTEGER_TYPE, 'system', 'chargingAmount6', '2300', 1, 0, 0],
            [SettingType::INTEGER_TYPE, 'system', 'promotionAmount6', '2430', 1, 0, 0],
        ]);
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     */
    public function down() {
        $this->dropTable('{{%setting}}');
    }
}
