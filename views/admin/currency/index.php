<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\grid\ActionColumn;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\HistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Currency');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="history-index">
    <?=GridView::widget([
        'dataProvider' => $dataProvider,
        'columns'      => [
            'id',
            'name',
            'number',
            [
                'class'    => ActionColumn::class,
                'template' => '{update}',
            ],
        ],
    ]);?>

</div>
