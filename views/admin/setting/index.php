<?php

use rmrevin\yii\fontawesome\FAS;
use app\models\enumerables\SettingType;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap4\Html;
use yii\widgets\Pjax;

/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $searchModel app\models\search\SettingSearch */

$this->title = Yii::t('yii2mod.settings', 'Settings');
if ($searchModel->section) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Settings'), 'url' => ['index']];
    $this->params['breadcrumbs'][] = ucfirst($searchModel->section);
} else {
    $this->params['breadcrumbs'][] = $this->title;
}
?>
<div class="table">
    <?= $this->render('tabs', ['searchModel' => $searchModel]) ?>
    <?php Pjax::begin(['timeout' => 10000, 'enablePushState' => true, 'class' => 'content p-0']); ?>
    <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns'      => [
                [
                    'class' => 'yii\grid\SerialColumn',
                ],
                /*[
                    'attribute'          => 'type',
                    'value'              => function ($model) {
                        return SettingType::listData()[$model->type];
                    },
                    'filter'             => SettingType::listData(),
                    'filterInputOptions' => ['prompt' => Yii::t('yii2mod.settings', 'Select Type'), 'class' => 'form-control'],
                ],
                [
                    'attribute'          => 'section',
                    'filter'             => ArrayHelper::map(SettingModel::find()->select('section')->distinct()->all(), 'section', 'section'),
                    'filterInputOptions' => ['prompt' => Yii::t('yii2mod.settings', 'Select Section'), 'class' => 'form-control'],
                ],*/
                [
                    'attribute' => 'key',
                    'format'    => 'html',
                    'value'     => function ($model) {
                        $tr = Yii::t('setting', $model->key);
                        return $model->key . ($tr != $model->key ? '<br/>' . $tr : '');
                    },
                ],
                [
                    'attribute' => 'value',
                    'format'    => 'html',
                    'value'     => function ($model) {
                        if ($model->type == SettingType::BOOLEAN_TYPE) {
                            return FAS::i($model->value ? 'check text-success' : 'times text-danger');
                        }
                        return $model->getSetting(null, null, '<br/>');
                    },
                ],
                [
                    'class'     => 'app\components\columns\ToggleColumn',
                    'attribute' => 'status',
                    'readonly'  => function ($model) {
                        return $model->system;
                    },
                ],
                [
                    'class'     => 'app\components\columns\ToggleColumn',
                    'attribute' => 'system',
                    'readonly'  => true,
                ],
                //'description:ntext',
                [
                    'header'   => Yii::t('yii2mod.settings', 'Actions'),
                    'class'    => 'app\components\columns\ActionColumn',
                    'template' => '{update}',
                    'buttons'  => [
                        'update' => function ($url, $model, $key) {
                            $icon = Html::tag('span', '', ['class' => "fas fa-edit text-success"]);
                            return $model->system < 2 ? Html::a($icon, $url) : '';
                        },
                    ],
                ],
            ],
        ]
    ); ?>
    <?php Pjax::end(); ?>
</div>
