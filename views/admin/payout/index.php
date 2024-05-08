<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use rmrevin\yii\fontawesome\FAS;
use app\models\Payout;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\PayoutSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Payouts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payout-index">
    <?php Pjax::begin(); ?>
    <?=GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'rowOptions'   => function ($model, $key, $index, $grid) {
            return [
                'class' => $model->status == Payout::STATUS_ACTIVE ? 'text-success' : ($model->status == Payout::STATUS_REJECT ? 'text-danger' : ''),
            ];
        },
        'columns'      => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'created_at',
                'format'    => 'datetime',
                'filter'    => \yii\jui\DatePicker::widget(['model' => $searchModel, 'attribute' => 'created_at', 'options' => ['class' => 'form-control']]),
            ],

            //'id',
            //'balance_id',
            [
                'label'     => Yii::t('admin', 'Username'),
                'attribute' => 'username',
                'value'     => 'user.username',
            ],
            [
                'label'     => Yii::t('account', 'Sum'),
                'attribute' => 'amount',
                'value'     => function ($model) {
                    $s = $model->status == Payout::STATUS_ACTIVE ? '+ ' : ($model->status == Payout::STATUS_REJECT ? '- ' : '');
                    return $s . Yii::$app->formatter->asCurrency($model->amount);
                },
            ],
            [
                'label'     => Yii::t('admin', 'Sum with fee'),
                'attribute' => 'amount',
                'filter'    => '',
                'value'     => function ($model) {
                    $s = $model->status == Payout::STATUS_ACTIVE ? '+ ' : ($model->status == Payout::STATUS_REJECT ? '- ' : '');
                    $fee = Yii::$app->settings->get('system', 'payoutFee');
                    return $s . Yii::$app->formatter->asCurrency($model->amount * (100 - $fee) / 100);
                },
            ],
            [
                'label' => Yii::t('site', 'Payments'),
                'attribute' => 'wallet_type',
                'value' => function($model) {
                    switch ($model->wallet_type) {
                        case 'payeer':
                            return 'Payeer wallet';
                        case 'perfect':
                            return 'Perfect money';
                        case 'tether':
                            return 'Tether USDT (Bep20)';
                        case 'banki_rf':
                            return 'Банки РФ';
                        case 'dc':
                            return 'DC wallet (next)';
                    }
                }
            ],
            [
                'label' => Yii::t('site', 'Payments number'),
                'attribute' => 'wallet_type',
                'value' => function($model) {
                    $user = User::findIdentity($model->user_id);
                    switch ($model->wallet_type) {
                        case 'payeer':
                            return $user->wallet;
                        case 'perfect':
                            return $user->wallet_perfect;
                        case 'tether':
                            return $user->wallet_tether;
                        case 'banki_rf':
                            return $user->wallet_banki_rf;
                        case 'dc':
                            return $user->wallet_dc;
                    }
                }
            ],
            //'history_id',
            //'created_at:dateTime',
            //'updated_at',
            [
                'class'     => 'app\components\columns\ToggleColumn',
                'attribute' => 'status',
                'readonly'  => true,
                'asLabel'   => true,
            ],
            'comment',
            [
                'class'    => ActionColumn::class,
                'template' => '{reject} {process}',
                'buttons'  => [
                    'reject'  => function ($url, $model, $key) {
                        return $model->status == 0 ? Html::a(FAS::i('undo text-danger'), $url, [
                            'title'        => 'Reject',
                            'data-confirm' => Yii::t('admin', 'Are you sure you want to reject this payout request?'),
                        ]) : '';
                    },
                    'process' => function ($url, $model, $key) {
                        return $model->status == 0 ? Html::a(FAS::i('check text-success'), $url, [
                            'title'        => 'Process',
                            'data-confirm' => Yii::t('admin', 'Are you sure you want to process this payout request?'),
                        ]) : '';
                    },
                ],
            ],
        ],
    ]);?>
    <?php Pjax::end(); ?>
</div>
