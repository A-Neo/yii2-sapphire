<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\TourSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Tours');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tour-index">

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?=GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => [
            ['class' => 'yii\grid\SerialColumn'],

            'user.username',
            //'passport',
            [
                'attribute' => 'passport',
                'format'    => 'raw',
                'value'     => function ($model) {
                    return Html::img(['/pm/tour/passport'], ['style' => 'height:100px']);
                },
            ],
            'number',
            'whatsapp',
            [
                'class'           => 'app\components\columns\EditableColumn',
                'attribute'       => 'status',
                'type'            => 'select',
                'value'           => function ($model) {
                    return $model->statusName;
                },
                'editableOptions' => function ($model) {
                    return [
                        'source' => $model->getStatusesList(),
                        'mode'   => 'inline',
                    ];
                },
            ],
            //'created_at',
            //'updated_at',
            /*[
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Tour $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],*/
        ],
    ]);?>

    <?php Pjax::end(); ?>

</div>
