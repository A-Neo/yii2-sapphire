<?php

use yii\bootstrap4\Html;

/* @var $this \yii\web\View */
/* @var $model \yii2mod\settings\models\SettingModel */

$this->title = Yii::t('yii2mod.settings', 'Update Setting: {0} -> {1}', [$model->section, $model->key]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('yii2mod.settings', 'Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('yii2mod.settings', 'Update Setting');
?>
<div class="table">
    <?php echo $this->render('_form', [
        'model' => $model,
    ]);?>
</div>
