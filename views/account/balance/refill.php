<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use app\components\Api;
use app\widgets\Tabs;
use app\models\History;
use app\models\Currency;

/* @var $this yii\web\View */

$this->title = Yii::t('account', 'Refill');
/**
 * @var $user \app\models\User
 * @var $curr string
 */
$user = Yii::$app->user->identity;
?>
<?=$this->render('_tabs')?>
<div class="row w-100">
    <div class="col-xs-12 col-sm-8 col-md-6 pl-3">
        <div class="wrapper_form-balance">
            <?php if(TEST_MODE): ?>
                <!--
                <div class="alert alert-danger"><?=Yii::t('account', 'Test mode!')?></div>
                <div class="alert alert-warning"><?=Yii::t('account', 'After testing all data will be erased!')?></div>
                -->
            <?php endif ?>
            <div class="site-signup">
                <?php $items = [];
                foreach(Currency::find()->all() as $currency):
                    ob_start();
                    ?>
                    <?php if($currency['name'] == History::CURRENCY_TETHER) { ?>
                    <p class="wallet"><?=Yii::t('account', 'Please send only {currency} to system wallet: <b>{wallet}</b><br>all other coins will be lost', ['currency' => $currency['name'], 'wallet' => $currency['number']])?></p>
                    <p><?=Yii::t('account', 'The money will be credited to your wallet, after we receive at least 1 confirmation from TRON-network')?></p>
                    <p><?=Yii::t('account', 'After that, fill the form and click the "Refill" button');?></p>
                    <p><?=Yii::t('account', 'Minimal refill amount {amount}', ['amount' => Api::asNumber(9, false)]);?> <b>USDT</b></p>
                    <?php } elseif($currency['name'] == History::CURRENCY_TRX) { ?>
                    <p class="wallet"><?=Yii::t('account', 'Please send only {currency} to system wallet: <b>{wallet}</b><br>all other coins will be lost', ['currency' => $currency['name'], 'wallet' => $currency['number']])?></p>
                    <p><?=Yii::t('account', 'The money will be credited to your wallet, after we receive at least 1 confirmation from TRON-network')?></p>
                    <p><?=Yii::t('account', 'After that, fill the form and click the "Refill" button');?></p>
                    <p><?=Yii::t('account', 'Minimal refill amount {amount}', ['amount' => Api::asNumber(10, false)]);?> <b>TRX</b></p>
                    <?} else { ?>
                    <p class="wallet"><?=Yii::t('account', 'Please send required amount {currency} to system wallet: <b>{wallet}</b>', ['currency' => $currency['name'], 'wallet' => $currency['number']])?>:</p>
                    <p><?=Yii::t('account', 'After that, fill the form and click the "Refill" button');?></p>
                    <p><?=Yii::t('account', 'Minimal refill amount {amount}', ['amount' => Api::asNumber(1)]);?></p>
                    <?php } ?>
                    <div class="row w-100">
                        <div class="col-12">
                            <?php $form = ActiveForm::begin(['id' => 'activation-form']); ?>
                            <input type="hidden" name="Tx[currency]" value="<?=$currency['name']?>"/>
                            <div class="form-group required">
                                <label for="tx-amount"><?=Yii::t('account', 'Sent amount')?></label>
                    <?php if($currency['name'] == History::CURRENCY_TETHER) { ?>
                                <input type="number" min="9" step="0.01" id="tx-amount" class="form-control" name="Tx[amount]" value="" autofocus="" required="true">
                    <?php } elseif($currency['name'] == History::CURRENCY_TRX) { ?>
                                <input type="number" min="10" step="1" id="tx-amount" class="form-control" name="Tx[amount]" value="" autofocus="" required="true">
                    <?php } else { ?>
                                <input type="number" min="1" step="0.01" id="tx-amount" class="form-control" name="Tx[amount]" value="" autofocus="" required="true">
                    <?php } ?>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-group required">
                    <?php if($currency['name'] == History::CURRENCY_TETHER || $currency['name'] == History::CURRENCY_TRX) { ?>
                                <label for="tx-id">Хэш транзакции</label>
                                <input type="text" id="tx-hash" class="form-control" name="Tx[hash]" value="" autofocus="" required="true">
                    <?php } else { ?>
                                <label for="tx-id"><?=Yii::t('account', 'Transaction ID')?></label>
                                <input type="text" id="tx-id" class="form-control" name="Tx[id]" value="" autofocus="" required="true">
                    <?php } ?>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-group">
                                <?=Html::submitButton(Yii::t('account', 'Refill'), ['class' => 'btn btn-primary', 'name' => 'signup-button'])?>
                            </div>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                    <?php
                    $items[] = [
                        'label'   => $currency['name'],
                        'content' => ob_get_contents(),
                        'active' => $curr == $currency['name'],
                    ];
                    ob_end_clean();
                endforeach;
                ?>
                <?=Tabs::widget([
                    'options'     => [
                        'tag'   => 'div',
                        'class' => 'wrapp_block-btn-stol mb-3',
                    ],
                    'encodeLabels' => false,
                    'linkOptions' => [
                        'class' => [
                            'widget'   => 'btn_stol w-button',
                            'activate' => 'on',
                        ],
                    ],
                    'items'       => $items,
                ]);?>
            </div>
        </div>
    </div>
</div>

