<?php $id = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id ?>
<?= \yii\bootstrap4\Tabs::widget([
    'options'       => [
        'tag'   => 'div',
        'class' => 'wrapp_block-btn-stol mb-3',
    ],
    'headerOptions' => [
        'tag'   => '',
    ],
    'linkOptions' => [
        'class' => [
            'widget'   => 'btn_stol w-button',
            'activate' => 'on',
        ],
    ],
    'items' => [
        [
            'label'  => Yii::t('account', 'Profile'),
            'url'    => ['/pm/profile'],
            'active' => $id == 'profile/index',
        ],
        [
            'label'  => Yii::t('account', 'Edit'),
            'url'    => ['/pm/profile/edit'],
            'active' => $id == 'profile/edit',
        ],
        [
            'label'  => Yii::t('account', 'Password'),
            'url'    => ['/pm/profile/password'],
            'active' => $id == 'profile/password',
        ],
        [
            'label'  => Yii::t('account', 'Financial password'),
            'url'    => ['/pm/profile/fin-password'],
            'active' => $id == 'profile/fin-password',
        ],
        [
            'label'  => Yii::t('account', 'Referral link'),
            'url'    => ['/pm/profile/link'],
            'active' => $id == 'profile/link',
        ],
    ],
]); ?>