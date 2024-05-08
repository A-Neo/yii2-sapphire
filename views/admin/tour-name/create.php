<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TourName */

$this->title = Yii::t('admin', 'Create tour');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Tours'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tour-name-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
