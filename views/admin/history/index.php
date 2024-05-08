<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\HistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'History');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="history-index">
    <?php Pjax::begin(); ?>
    <?=GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => [
            'id',
            'date',
            //'type',
            'status',
            [
                'attribute' => 'user_id',
                'value'     => function ($model) {
                    return $model->user ? $model->user->username : null;
                },
            ],
            'from',
            //'debitedAmount',
            //'debitedCurrency',
            'to',
            'creditedAmount',
            'payeerFee',
            'creditedCurrency',
            //'payeerFee',
            //'gateFee',
            //'exchangeRate',
            //'protect',
            'comment',
            /*[
                'attribute' => 'creditedAmount',
                'value'     => function ($model) {
                    var_dump($model->getAttributes());exit;
                },
            ]*/
            //'isApi',
            /*[
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, History $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],*/
        ],
    ]);?>

    <?php Pjax::end(); ?>

</div>
