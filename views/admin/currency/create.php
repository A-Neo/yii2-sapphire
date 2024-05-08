<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = Yii::t('admin', 'Create Currency');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Currency'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create table">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
