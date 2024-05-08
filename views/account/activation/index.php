<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use app\components\Api;

/* @var $this yii\web\View */
/* @var $clone int */
$this->title = $clone ? Yii::t('account', 'Buy clone') : Yii::t('account', 'Activation');
/**
 * @var $user  \app\models\User
 * @var $user1 \app\models\User|null
 */
$user = Yii::$app->user->identity;
if(empty(Yii::$app->params['api']['account'])){
    return 'Empty api params';
}

?>
<div class="row w-100 mt-3">
    <div class="col-xs-12 col-md-6">
        <div class="wrapper_form-balance">
            <?php if(TEST_MODE): ?>
                <div class="alert alert-danger"><?=Yii::t('account', 'Test mode!')?></div>
                <div class="alert alert-warning"><?=Yii::t('account', 'After testing all data will be erased!')?></div>
            <?php endif ?>
            <?php if(!empty($user1)): ?>
                <div class="alert alert-info"><?=Yii::t('account', 'Partner activation from yours balance!')?> <b><?=$user1->username?></b></div>
            <?php endif ?>
            <div class="site-signup">
                <p><?=
                    $clone ?
                        Yii::t('account', 'Amount required for buy clone<b>{amount}</b>.', ['amount' => Api::asNumber($amount = Yii::$app->settings->get('system', 'promotionAmount1'))]) :
                        Yii::t('account', 'Amount required for activation <b>{amount}</b>.', ['amount' => Api::asNumber($amount = Yii::$app->settings->get('system', 'activationAmount'))])
                    ?>
                    <?php
                    $has = number_format($user->balance - $user->accumulation, 2, '.', '');
                    $required = number_format($amount - $user->balance - $user->accumulation, 2, '.', '');
                    ?>
                    <?php if($has > 0): ?>
                        <?=Yii::t('account', 'You have <b>{amount}</b> on account balance.', ['amount' => Api::asNumber($has)])?>
                    <?php endif ?>
                </p>
                <?php if($required > 0): ?>
                    <p><?=Yii::t('account', 'Please send missing amount <b>{amount}</b> PAYEER to system wallet: <b>{wallet}</b>', ['amount' => Api::asNumber($required), 'wallet' => Yii::$app->params['api']['account']])?>
                        :</p>
                    <p><?=Yii::t('account', 'After that, fill the form and click the "Activate" button');?></p>
                    <p><?=Yii::t('account', 'For activation balance must be equal or more <b>{amount}</b>.', ['amount' => Api::asNumber($amount)]);?></p>
                <?php endif ?>
                <div class="row">
                    <div class="col-12">
                        <?php $form = ActiveForm::begin(['id' => 'activation-form']); ?>
                        <?php if($has < $amount): ?>
                            <div class="form-group required">
                                <label for="tx-amount"><?=Yii::t('account', 'Sent amount')?></label>
                                <input type="text" id="tx-amount" class="form-control" name="Tx[amount]" value="" autofocus="" required="true">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-group required">
                                <label for="tx-id"><?=Yii::t('account', 'Transaction ID')?></label>
                                <input type="text" id="tx-id" class="form-control" name="Tx[id]" value="" autofocus="" required="true">
                                <div class="invalid-feedback"></div>
                            </div>
                        <?php endif ?>
                        <div class="form-group">
                            <?=Html::submitButton($clone ? Yii::t('account', 'Buy') : Yii::t('account', 'Activate'), ['class' => 'btn btn-primary', 'name' => 'signup-button'])?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>