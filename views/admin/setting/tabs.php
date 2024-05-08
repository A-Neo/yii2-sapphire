<?php

/* @var $searchModel app\models\search\SettingSearch */
?>
<?php
if (empty($searchModel)) {
    $searchModel = new app\models\search\SettingSearch();
}
$tabs[] = [
    'label'  => Yii::t('admin', 'All'),
    'url'    => ['/cp/setting'],
    'active' => empty($searchModel->section) && !(Yii::$app->controller->action->id == 'export' || Yii::$app->controller->action->id == 'import'),
];
foreach ($searchModel->sections() as $section) {
    $tabs[] = [
        'label'  => ucfirst($section),
        'url'    => ['/cp/setting', $searchModel->formName() => ['section' => $section]],
        'active' => $searchModel->section == $section,
    ];
}

$tabs[] = [
    'label'         => Yii::t('admin', 'Export / Import'),
    'url'           => ['/cp/setting/export'],
    'active'        => Yii::$app->controller->action->id == 'export' || Yii::$app->controller->action->id == 'import',
    'headerOptions' => ['class' => ['widget' => 'ml-auto']],
];

?>
<?= \yii\bootstrap4\Tabs::widget([
    'items' => $tabs,
]) ?>