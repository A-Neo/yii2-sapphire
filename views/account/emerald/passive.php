<?php
use app\models\EmeraldMain;
use app\models\EmeraldDelay;
use app\models\EmeraldUsers;
use app\models\Payout;
use rmrevin\yii\fontawesome\FAS;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View
 * @var $levels EmeraldMain[]
 * @var $username string
 * @var $userid integer
 * @var $delayUsers EmeraldDelay[]
 */

$this->title = Yii::t('account', 'Пассивный доход от проекта «Emerald Health»');


$ok = Yii::$app->session->getFlash('okmessage', false);
$err = Yii::$app->session->getFlash('errmessage', false);


?>
<div class="row w-100">
    <div class="col-xs-12 col-sm-6 col-xl-3 p-3">
        <div class="inform_tour w-inline-block w-100">
            <div class="balance">Общая сумма</div>
            <div class="balance_amount">
                <span class="span text-nowrap">0 <?=FAS::icon('dollar-sign')?></span>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-xl-3 p-3">
        <div class="inform_tour w-inline-block w-100">
            <div class="balance">Месяцев</div>
            <div class="balance_amount">
                <span class="span text-nowrap">0</span>
            </div>
        </div>
    </div>
</div>
<div class="w-100 wrapper_form-balance">
    <?php Pjax::begin(); ?>
    <?=GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel'  => $searchModel,
        'rowOptions'   => function ($model, $key, $index, $grid) {
            return [
                'class' => $model->status == Payout::STATUS_ACTIVE ? 'text-success' : ($model->status == Payout::STATUS_REJECT ? 'text-danger' : ''),
            ];
        },
        'columns'      => [
            ['class' => 'yii\grid\SerialColumn'],
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
                    $user = Yii::$app->user->identity;
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
            [
                'label'     => Yii::t('account', 'Sum'),
                'attribute' => 'amount',
                'value'     => function ($model) {
                    $s = $model->status == Payout::STATUS_ACTIVE ? '+ ' : ($model->status == Payout::STATUS_REJECT ? '- ' : '');
                    return $s . Yii::$app->formatter->asCurrency($model->amount);
                },
            ],
            [
                'label'     => Yii::t('account', 'Amount receivable'),
                'attribute' => 'amount',
                'value'     => function ($model) {
                    // $fee = Yii::$app->settings->get('system', 'payoutFee');
                    $fee = $model->comission;
                    $s = $model->status == Payout::STATUS_ACTIVE ? '+ ' : ($model->status == Payout::STATUS_REJECT ? '- ' : '');
                    return $s . Yii::$app->formatter->asCurrency($model->amount * (100 - $fee) / 100);
                },
            ],
            [
                'class'     => 'app\components\columns\ToggleColumn',
                'attribute' => 'status',
                'readonly'  => true,
                'asLabel'   => true,
            ],
            'comment',
            //'created_at:dateTime',
            //'updated_at:dateTime',
        ],
    ]);?>
    <?php Pjax::end(); ?>
</div>

<script>
    // js

</script>
