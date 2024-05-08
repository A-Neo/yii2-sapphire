<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Payout;

/* @var $this yii\web\View */

$this->title = Yii::t('account', 'Payout');
/**
 * @var $user \app\models\User
 */
$user = Yii::$app->user->identity;
?>
<?=$this->render('_tabs')?>
<?php if(empty($user->wallet)): ?>
    <p><?=Yii::t('account', 'You need to setup payout wallet in account profile')?></p>
    <?php return ?>
<?php elseif(empty($user->fin_password)): ?>
    <p><?=Yii::t('account', 'You need to setup financial password in account profile')?></p>
    <?php return ?>
<?php endif ?>
<div class="row w-100">
    <div class="col-xs-12 col-sm-8 col-md-6 pl-3">
        <div class="wrapper_form-balance">
            <p><b><?=Yii::t('account', 'IMPORTANT❗️Withdrawals are processed within 24 hours')?></b></p>
            <?php $form = ActiveForm::begin(['id' => 'activation-form']); ?>
            <div class="form-group required">
                <label for="tx-amount"><?=Yii::t('account', 'Payout will be proceed to wallet')?></label>
                <select name="Tx[wallet_type]" required class="form-control wallet_type">
                    <option value="" selected>-</option>
                    <option value='payeer' <?php if (!$user->wallet) echo 'disabled'?>><?=Yii::t('site', 'Wallet')?> (<?=$user->wallet?>)</option>
                    <option value='perfect'<?php if (!$user->wallet_perfect) echo 'disabled'?>><?=Yii::t('site', 'Wallet Perfect')?> (<?=$user->wallet_perfect?>)</option>
                    <option value='tether' <?php if (!$user->wallet_tether) echo 'disabled'?>><?=Yii::t('site', 'Wallet Tether')?> (<?=$user->wallet_tether?>)</option>
                    <option value='banki_rf' <?php if (!$user->wallet_banki_rf) echo 'disabled'?>><?=Yii::t('site', 'Wallet Banki RF')?> (<?=$user->wallet_banki_rf?>)</option>
                    <option value='dc' <?php if (!$user->wallet_dc) echo 'disabled'?>><?=Yii::t('site', 'Wallet DC')?> (<?=$user->wallet_dc?>)</option>
                </select>
            </div>
            <div class="form-group hide wallet_payeer wallet_payout_description">
                Комиссия 2%
            </div>
            <div class="form-group hide wallet_perfect wallet_payout_description">
                Комиссия 2%
            </div>
            <div class="form-group hide wallet_tether wallet_payout_description">
                Лимит: от 10$ до 20000$ в сутки<br/>
                Комиссия 2%
            </div>
            <div class="form-group hide wallet_banki_rf wallet_payout_description">
                Будьте внимательны при вводе номера карты, за неправильно набранный номер карты Sapphire company - ответственности не несёт<br/>
                Имя фамилия (необходимо вводить на латинице)<br/>
                Сумма:<br/>
                От 10$ до 1000$ (курс НБ)<br/>
                Комиссия 6%
            </div>
            <div class="form-group hide wallet_dc wallet_payout_description">
                Перевод возможен только на идентифицированные кошельки.<br/>
                Номер получателя<br/>
                +992......<br/>
                Сумма:<br/>
                От 10$ до 1000$ (курс НБ)<br/>
                Комиссия 4%
            </div>
            <div class="form-group required">
                <label for="tx-amount"><?=Yii::t('account', 'Amount to sent')?></label>
                <input type="number" step="0.01" max="<?=$user->balance - $user->accumulation?>" id="tx-amount" class="form-control" name="Tx[amount]" value="" autofocus="" required="true">
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-group required">
                <label for="tx-amount"><?=Yii::t('account', 'Financial password')?></label>
                <input type="password" id="tx-amount" class="form-control" name="Tx[password]" value="" autofocus="" required="true">
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-group required">
                <label for="tx-amount"><?=Yii::t('account', 'Comment')?></label>
                <textarea id="tx-comment" class="form-control" name="Tx[comment]" ></textarea>
                <div class="invalid-feedback"></div>
            </div>
            <div class="form-group">
                <?=Html::submitButton(Yii::t('account', 'Payout'), ['class' => 'btn btn-primary', 'name' => 'signup-button'])?>
            </div>
            <?php ActiveForm::end(); ?>
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