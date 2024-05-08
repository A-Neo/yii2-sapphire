<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Activation */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('site', 'Activations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="activation-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'table',
            'status',
            't1_left',
            't1_right',
            't2_left',
            't2_right',
            't3_left',
            't3_right',
            't4_left',
            't4_right',
            't5_left',
            't5_right',
            't6_left',
            't6_right',
            'created_at',
            'updated_at',
        ],
    ]) ?>
</div>
