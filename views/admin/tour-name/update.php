<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TourName */

$this->title = Yii::t('admin', 'Update tour: {name}', [
    'name' => $model->t_name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Tours'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->t_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('admin', 'Update');
?>
<div class="tour-name-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
