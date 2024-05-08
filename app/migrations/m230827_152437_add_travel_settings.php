<?php

use yii\db\Migration;

/**
 * Class m230827_152437_add_travel_settings
 */
class m230827_152437_add_travel_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settings = new \app\models\SettingModel();
        $plans = \app\models\TravelMain::getPlanList();

        for($i = 1; $i <= 8; $i++) {
            $settings->setSetting('System', 'Travel' . $i . '_contribution', $plans[$i]['contribution'], null, 1);
            $settings->setSetting('System', 'Travel' . $i . '_slot1', $plans[$i]['slot1'], null, 1);
            $settings->setSetting('System', 'Travel' . $i . '_slot2', $plans[$i]['slot2'], null, 1);
            $settings->setSetting('System', 'Travel' . $i . '_slot3', $plans[$i]['slot3'], null, 1);
            $settings->setSetting('System', 'Travel' . $i . '_refBalance', $plans[$i]['refBalance'], null, 1);
            $settings->setSetting('System', 'Travel' . $i . '_refBalanceSt', $plans[$i]['refBalanceSt'], null, 1);

        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $names = [];
        for($i = 1; $i <= 8; $i++) {
            $names[] = '"Travel' . $i . '_contribution"';
            $names[] = '"Travel' . $i . '_slot1"';
            $names[] = '"Travel' . $i . '_slot2"';
            $names[] = '"Travel' . $i . '_slot3"';
            $names[] = '"Travel' . $i . '_refBalance"';
            $names[] = '"Travel' . $i . '_refBalanceSt"';
        }

        $this->db->createCommand('DELETE FROM setting WHERE `key` IN (' . implode(',', $names) . ')')->execute();
    }

}
