<?php

use app\widgets\Tabs;

$id = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id
?>
<?=Tabs::widget([
    'options'       => [
        'tag'   => 'div',
        'class' => 'wrapp_block-btn-stol mb-3',
    ],
    'headerOptions' => [
        'tag' => '',
    ],
    'linkOptions'   => [
        'class' => [
            'widget'   => 'btn_stol w-button',
            'activate' => 'on',
        ],
    ],
    'items'         => [
        [
            'label'  => Yii::t('account', 'Balance'),
            'url'    => ['/pm/balance'],
            'active' => $id == 'balance/index',
        ],
        [
            'label'  => Yii::t('account', 'Refill'),
            'url'    => ['/pm/balance/refill'],
            'active' => $id == 'balance/refill',
        ],
        [
            'label'  => Yii::t('account', 'Transfer'),
            'url'    => ['/pm/balance/transfer'],
            'active' => $id == 'balance/transfer',
        ],
        [
            'label'  => Yii::t('account', 'Payout'),
            'url'    => ['/pm/balance/payout'],
            'active' => $id == 'balance/payout',
        ],
    ],
]);?>