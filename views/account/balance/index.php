<?php

use yii\helpers\Url;
use yii\bootstrap4\Html;
use app\components\Api;
use rmrevin\yii\fontawesome\FAS;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Balance;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\BalanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('account', 'Balance');
/**
 * @var $user \app\models\User
 */
$user = Yii::$app->user->identity;
?>
<?php /*if(!$user->isActive()): ?>
    <div class="card">
        <div class="card-body">
            <a href="<?=Url::to(['/pm/activation/index'])?>"><?=Yii::t('account', 'Activation required')?></a>
        </div>
    </div>
    <?php return ?>
<?php else:*/ ?>
<div class="balance_block">
    <?=$this->render('_tabs')?>
</div>
<?php /*endif */?>
<div class="row w-100">
    <div class="col-xs-12 col-sm-6 col-xl-3 p-3">
        <div class="inform_tour w-inline-block w-100">
            <div class="balance"><?=Yii::t('account', 'Balance')?></div>
            <div class="balance_amount">
                <span class="span text-nowrap"><?=number_format($user->balance - $user->accumulation, 2, '.', '')?> <?=FAS::icon('dollar-sign')?></span>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-xl-3 p-3">
        <div class="inform_tour w-inline-block w-100">
            <div class="balance"><?=Yii::t('account', 'Balance ST')?></div>
            <div class="balance_amount">
                <span class="span text-nowrap"><?=number_format($user->balance_travel, 2, '.', '')?> <?=FAS::icon('dollar-sign')?></span>
            </div>
        </div>
    </div>
</div>
<div class="w-100 wrapper_form-balance">
    <?php Pjax::begin(); ?>
    <?=GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'rowOptions'   => function ($model, $key, $index, $grid) {
            $s = $model->to_user_id == Yii::$app->user->id;
            $ss = $model->type == Balance::TYPE_ACCUMULATION;
            return [
                'class' => 'text-' . ($ss ? 'warning' : ($s ? 'success' : 'danger')),
            ];
        },
        'columns'      => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'type',
                'format'    => 'html',
                'value'     => function ($model) {
                    return $model->getTypeName();
                },
            ],
            [
                'attribute' => 'from_user_id',
                'value'     => 'fromUserName',
            ],
            [
                'attribute' => 'to_user_id',
                'value'     => 'toUserName',
            ],
            //'history_id',
            [
                'attribute' => 'from_amount',
                'format'    => 'html',
                'value'     => function ($model) {
                    $s = $model->to_user_id == Yii::$app->user->id;
                    if($model->from_sapphire > 0 || $model->to_sapphire > 0){
                        return ($s ? '+ ' : '- ') . ($s ? $model->to_sapphire : $model->from_sapphire) . ' ' . Yii::t('account', 'Points');
                    }
                    return ($s ? '+ ' : '- ') . Api::asNumber($s ? $model->to_amount : $model->from_amount);
                },
            ],
            [
                'class'     => 'app\components\columns\ToggleColumn',
                'attribute' => 'status',
                'readonly'  => 'true',
            ],
            'created_at:dateTime',
            'comment',
            //'updated_at:dateTime',
        ],
    ]);?>
    <?php Pjax::end(); ?>
</div>