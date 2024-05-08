<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\TourNameSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Tours');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tour-name-index">
    <p>
        <?=Html::a(Yii::t('admin', 'Create tour'), ['create'], ['class' => 'btn btn-success'])?>
    </p>
    <?php Pjax::begin(); ?>
    <?=GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => [
            ['class' => 'yii\grid\SerialColumn'],

            'price',
            'name:tString',
            [
                'class'     => 'app\components\columns\ToggleColumn',
                'attribute' => 'status',
            ],
            [
                'class'    => ActionColumn::class,
                'template' => '{update} {delete}',
            ],
        ],
    ]);?>
    <?php Pjax::end(); ?>
</div>
