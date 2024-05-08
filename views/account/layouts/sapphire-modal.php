<?php
use yii\bootstrap4\ActiveForm;
?>
<div class="modal" id="sapphire-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?=Yii::t('account', 'Sapphire points waiting for request')?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><?=Yii::t('account', 'You have sapphire points available to accrual.')?></p>
                <p><?=Yii::t('account', 'You need send request for support to accrual them.')?></p>
                <?php $form = ActiveForm::begin(['id' => 'request-form', 'action' => '/pm/balance']); ?>
                <div class="form-group required">
                    <label for="tx-amount"><?=Yii::t('account', 'Comment')?> <b class="text-danger">*</b></label>
                    <textarea id="tx-comment" class="form-control" name="Tx[comment]" required="required"></textarea>
                    <div class="invalid-feedback"></div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary disabled" disabled id="send-request"><?=Yii::t('account', 'Send request')?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=Yii::t('account', 'Close')?></button>
            </div>
        </div>
    </div>
</div>