<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\NewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'News');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="table">
    <?= Html::a(Yii::t('admin', 'Create News'), ['create'], ['class' => 'btn btn-success']) ?>
</div>

<?php Pjax::begin(['options' => ['class' => 'content p-0']]); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'attribute' => 'published_at',
            'format'    => 'datetime',
            'filter'    => \yii\jui\DatePicker::widget(['model' => $searchModel, 'attribute' => 'published_at', 'options' => ['class' => 'form-control']]),
        ],
        'title:tstring',

        [
            'class'     => 'app\components\columns\ToggleColumn',
            'attribute' => 'status',
        ],

        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',
        ],
    ],
]); ?>

<?php Pjax::end(); ?>

</div>
