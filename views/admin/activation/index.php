<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Activation;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ActivationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
if(!empty($_GET['enable'])){
    Yii::$app->session->remove('disableAutoSet');
}
$this->title = Yii::t('site', 'Activations');
$this->params['breadcrumbs'][] = $this->title;
if(Yii::$app->session->get('disableAutoSet', false)):?>
    <a href="?enable=1" class="btn btn-success"><?=Yii::t('admin', 'Automatically installation is disabled. Enable it Now.')?></a>
<?php endif ?>
<div class="activation-index">
    <?php Pjax::begin(); ?>
    <?=GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'created_at',
                'format'    => 'datetime',
                'filter'    => \yii\jui\DatePicker::widget(['model' => $searchModel, 'attribute' => 'created_at', 'options' => ['class' => 'form-control']]),
            ],
            'clone',
            'table',
            [
                'attribute' => 'username',
                'label'     => Yii::t('site', 'Username'),
                'value'     => 'user.username',
            ],
            [
                'attribute' => 'topusername',
                'format'    => 'raw',
                'label'     => Yii::t('site', 'Top username'),
                'value'     => function ($model) {
                    $t = '';
                    for($i = $model->start; $i <= $model->table; $i++){
                        $top = $model->getTop($i);
                        if(!$top) continue;
                        $t .= $i . ': ' . $top->user->username . ($top->clone ? '(' . $top->clone . ')' : '') . '<br>';
                    }
                    return $t;
                },
            ],
            [
                'attribute' => 'toptopusername',
                'format'    => 'raw',
                'label'     => Yii::t('site', 'Top top username'),
                'value'     => function ($model) {
                    $t = '';
                    for($i = $model->start; $i <= $model->table; $i++){
                        $top = $model->getTop($i);
                        if(!$top) continue;
                        $top = $top->getTop($i);
                        if(!$top) continue;
                        $t .= $i . ': ' . $top->user->username . ($top->clone ? '(' . $top->clone . ')' : '') . '<br>';
                    }
                    return $t;
                },
            ],
            //'table',
            [
                'class'     => 'app\components\columns\ToggleColumn',
                'attribute' => 'status',
                'readonly'  => 'true',
            ],
            //'t1_left',
            //'t1_right',
            //'t2_left',
            //'t2_right',
            //'t3_left',
            //'t3_right',
            //'t4_left',
            //'t4_right',
            //'t5_left',
            //'t5_right',
            //'t6_left',
            //'t6_right',
            //'created_at',
            //'updated_at',
            [
                'class'    => 'app\components\columns\ActionColumn',
                'template' => '{return}',
                'buttons'  => [
                    'return' => function ($url, $model, $key) {
                        $t = $model->table;
                        if(!empty($model->{"t{$t}_left"}) || !empty($model->{"t{$t}_right"})){
                            return '';
                        }
                        $top = $model->getTop($t);
                        if(empty($top) || /*$top->table > $t || */ $top->status == Activation::STATUS_CLOSED){
                            return '';
                        }
                        $topTop = $top->getTop($t);
                        if(empty($topTop) || /*$topTop->table > $t ||*/ $topTop->status == Activation::STATUS_CLOSED){
                            return '';
                        }
                        $icon = Html::tag('span', '', [
                            'class' => 'fas fa-undo text-danger',
                        ]);
                        return Html::a($icon, $url, [
                            'title'        => Yii::t('admin', 'Return into stop list'),
                            'data-confirm' => Yii::t('admin', 'Are you sure you want to return this item to stop list?'),
                        ]);
                    },
                ],
            ],
        ],
    ]);?>
    <?php Pjax::end(); ?>
</div>
