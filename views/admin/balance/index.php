<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap4\Html;
use app\components\Api;
use app\models\Balance;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\BalanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Balances');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="balance-index">
    <?php Pjax::begin(); ?>
    <?=GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'rowOptions'   => function ($model, $key, $index, $grid) {
            $s = !is_null($model->to_user_id);
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
                    if($model->from_sapphire > 0){
                        return $model->from_sapphire ? '- ' . $model->from_sapphire . ' ' . Yii::t('account', 'Points') : '';
                    }
                    return $model->from_amount ? '- ' . Api::asNumber($model->from_amount) : '';
                },
            ],
            [
                'attribute' => 'to_amount',
                'format'    => 'html',
                'value'     => function ($model) {
                    if($model->to_sapphire > 0){
                        return $model->to_sapphire ? '+ ' . $model->to_sapphire . ' ' . Yii::t('account', 'Points') : '';
                    }
                    return $model->to_amount ? '+ ' . Api::asNumber($model->to_amount) : '';
                },
            ],
            [
                'class'     => 'app\components\columns\ToggleColumn',
                'attribute' => 'status',
                'readonly'  => 'true',
            ],
            'comment',
            [
                'attribute' => 'created_at',
                'format'    => 'datetime',
                'filter'    => \yii\jui\DatePicker::widget(['model' => $searchModel, 'attribute' => 'created_at', 'options' => ['class' => 'form-control']]),
            ],
            [
                'attribute' => '_id',
                'label'     => '',
                'format'    => 'raw',
                'value'     => function ($model) {
                    if($model->status == Balance::STATUS_WAITING){
                        return Html::a(Yii::t('admin', 'Approve'), ['/cp/balance/approve', 'id' => $model->id], [
                                'class'        => 'btn btn-success',
                                'data-confirm' => Yii::t('admin', 'Attention. This action cannot be reverted'),
                            ]) . ' ' . Html::a(Yii::t('admin', 'Reject'), ['/cp/balance/reject', 'id' => $model->id], [
                                'class'        => 'btn btn-danger',
                                'data-confirm' => Yii::t('admin', 'Attention. This action cannot be reverted'),
                            ]);

                    }
                    if($model->type == Balance::TYPE_TRANSFER && !empty($model->fromUser) && !empty($model->toUser) && ($model->toUser->balance - $model->toUser->accumulation >= $model->to_amount)){
                        return Html::a(Yii::t('admin', 'Return to sender'), ['/cp/balance/return', 'id' => $model->id], [
                            'class'        => 'btn btn-warning',
                            'data-confirm' => Yii::t('admin', 'Attention. From the recipient to the sender, the received amount will be transferred minus the commission for the transfer.'),
                        ]);
                    }
                    return '';
                },
            ]
            //'updated_at:dateTime',
        ],
    ]);?>
    <?php Pjax::end(); ?>

</div>
