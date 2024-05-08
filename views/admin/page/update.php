<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Page */

$this->title = Yii::t('admin', 'Update Page: {name}', [
    'name' => $model->t_title,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Pages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('admin', 'Update');
?>
<div class="table">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
