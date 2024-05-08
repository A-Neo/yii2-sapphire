<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\News */

$this->title = Yii::t('admin', 'Create News');
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'News'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="table">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
