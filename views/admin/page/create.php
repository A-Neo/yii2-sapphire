<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Page */

$this->title = Yii::t('admin', 'Create Page');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="table">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
