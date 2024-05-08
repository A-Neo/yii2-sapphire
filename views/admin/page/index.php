<?php

use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\PageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Pages');
$this->params['breadcrumbs'][] = $this->title;
/*$systemPages = '';
if (!empty($slugs)) {
    $rows = '';
    foreach ($slugs as $slug) {
        $rows .= Html::tag('tr',
            Html::tag('td', Html::a($url = Url::to(['/page/show', 'slug' => $slug]), $url), ['class' => 'p-2']) .
            Html::tag('td', Html::a(Yii::t('admin','Create'), ['create', 'slug' => $slug]), ['class' => 'text-nowrap p-2 text-center'])
        );
    }
    $systemPages = Html::tag('table', Html::tag('tbody', $rows), ['class' => 'invoices-table']);
}*/
?>
123
<div class="table">
    <?=Html::a(Yii::t('admin', 'Create Page'), ['create'], ['class' => 'btn btn-success'])?>
</div>

<?php Pjax::begin(['options' => ['class' => 'content p-0']]); ?>

<?=GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    //'layout'       => "{summary}\n{items}{$systemPages}\n{pager}",
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'position',
            'class' => 'app\components\columns\PositionColumn',
        ],
        [
            'attribute' => 'slug',
            'format'    => 'raw',
            'value'     => function ($model) {
                $url = Url::to(['/page/show', 'slug' => $model->slug]);
                return Html::a($url, $url);
            },
        ],
        'title:tstring',

        [
            'class'     => 'app\components\columns\ToggleColumn',
            'attribute' => 'status',
        ],
        //'created_at:dateTime',
        //'updated_at:dateTime',

        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',
        ],
    ],
]);?>

<?php Pjax::end(); ?>
