<?php

/* @var $this yii\web\View */
/* @var $model app\models\forms\ImportForm */

$this->title = Yii::t('admin', 'Export / Import');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('tabs') ?>
<?= $this->render('//cp/export', ['controller' => 'setting', 'model' => $model]) ?>
