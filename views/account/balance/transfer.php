<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('account', 'Transfer');
/**
 * @var $user \app\models\User
 */
$user = Yii::$app->user->identity;
$fee = Yii::$app->settings->get('system', 'transferFee');
$this->registerJs(<<<JS
function calculateReceive(){
    fee=$fee;
    val =$('#tx-amount').val();
    fee = Math.ceil(val*fee)/100;
    $('#tx-amount-receive').val(val-fee);
}
calculateReceive();
$('#tx-amount').on('change keyup paste click', function (){
    calculateReceive();    
});
JS
);
?>
<?=$this->render('_tabs')?>
<div class="row w-100">
    <div class="col-xs-12 col-sm-8 col-md-6 pl-3">
        <div class="wrapper_form-balance">
            <p><?=Yii::t('account', 'Transfer fee: <b>{fee}%</b>', ['fee' => $fee])?>:</p>
            <?php $form = ActiveForm::begin(['id' => 'activation-form']); ?>
            <div class="form-group required">
                <label for="tx-amount"><?=Yii::t('account', 'Amount to sent')?></label>
                <div class="input-group">
                    <input type="number" min="0.01" step="0.01" max="<?=$user->balance - $user->accumulation?>" id="tx-amount" class="form-control" name="Tx[amount]" value="<?=$user->balance - $user->accumulation > 1 ? 1 : 0?>"
                           autofocus="" required="true">
                    <div class="input-group-append">
                        <div class="input-group-text">$</div>
                    </div>
                </div>
                <div class="invalid-feedback"></div>
            </div>
            <!--
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
            -->
            <div class="form-group field-user-username required">
                <label for="tx-to"><?=Yii::t('account', 'Recipient')?></label>
                <input type="text" id="tx-to" class="form-control" name="Tx[to]" value="" autofocus="" required="true">
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
                <?=Html::submitButton(Yii::t('account', 'Transfer'), ['class' => 'btn btn-primary', 'name' => 'signup-button'])?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>


