<?php

use yii\db\Migration;

/**
 * Class m220501_180009_fix_fee_type
 */
class m220501_180009_fix_fee_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->update('{{%setting}}', [
            'type' => \app\models\enumerables\SettingType::FLOAT_TYPE,
        ], ['key' => ['transferFee', 'payoutFee']]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->update('{{%setting}}', [
            'type' => \app\models\enumerables\SettingType::INTEGER_TYPE,
        ], ['key' => ['transferFee', 'payoutFee']]);
    }

}
