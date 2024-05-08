<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\components\Api;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = Yii::t('admin', 'Transfer money from User: {name}', [
    'name' => $model->username,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('admin', 'Transfer');
?>
<div class="user-update table row">
    <div class="user-form col-xs-12 col-md-6 log-lx-4">
        <?php $form = ActiveForm::begin(['id' => 'activation-form']); ?>
        <div class="form-group required">
            <label for="tx-amount"><?=Yii::t('account', 'Amount to sent')?></label>
            <div class="input-group">
                <input type="number" min="1" step="0.01" max="<?=Api::asNumber($model->balance - $model->accumulation, false)?>" id="tx-amount" class="form-control" name="Tx[amount]"
                       value="<?=$model->balance - $model->accumulation > 1 ? 1 : 0?>"
                       autofocus="" required="true">
                <div class="input-group-append">
                    <div class="input-group-text">$</div>
                </div>
            </div>
            <div class="invalid-feedback"></div>
            <div class="input-hint"><?=Yii::t('admin', 'Available for transfer')?> <?=Api::asNumber($model->balance - $model->accumulation)?></div>
        </div>
        <div class="form-group required">
            <label for="tx-amount"><?=Yii::t('account', 'Amount to receive')?></label>
            <div class="input-group">
                <input type="number" id="tx-amount-receive" class="form-control" value="" readonly>
                <div class="input-group-append">
                    <div class="input-group-text">$</div>
                </div>
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="form-group field-user-username required">
            <label for="tx-to"><?=Yii::t('account', 'Recipient')?></label>
            <input type="text" id="tx-to" class="form-control" name="Tx[to]" value="" autofocus="" required="true">
            <div class="invalid-feedback"></div>
        </div>
        <div class="form-group">
            <?=Html::submitButton(Yii::t('account', 'Transfer'), ['class' => 'btn btn-primary', 'name' => 'signup-button'])?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
