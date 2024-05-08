<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\components\Api;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $cur string */
/* @var $mod string */
if($mod == 'plus'){
    $label = Yii::t('admin', 'Amount for charge');
    $button = Yii::t('admin', 'Charge');
}else{
    $label = Yii::t('admin', 'Amount for withdraw');
    $button = Yii::t('admin', 'Withdraw');
}
if($cur == 'balance'){
    if($mod == 'plus'){
        $title = Yii::t('admin', 'Charge to user\'s balance');
    }else{
        $title = Yii::t('admin', 'Withdraw from user\'s balance');
    }
}else{
    if($mod == 'plus'){
        $title = Yii::t('admin', 'Charge to user\'s sapphire');
    }else{
        $title = Yii::t('admin', 'Withdraw from user\'s sapphire');
    }
}
$this->title = $title . ' ' . Yii::t('admin', 'User: {name}', ['name' => $model->username]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $mod == 'plus' ? Yii::t('admin', 'Charge') : Yii::t('admin', 'Withdraw');
?>
<div class="user-update table row">
    <div class="user-form col-xs-12 col-md-6 log-lx-4">
        <?php $form = ActiveForm::begin(['id' => 'charge-form']); ?>
        <div class="form-group required">
            <label for="tx-amount"><?=$label?></label>
            <div class="input-group">
                <input type="number" min="0.01" step="0.01" id="amount" class="form-control" name="amount" value="" autofocus="" required="true">
                <?php if($cur == 'balance'): ?>
                    <div class="input-group-append">
                        <div class="input-group-text">$</div>
                    </div>
                <?php endif ?>
            </div>
            <div class="invalid-feedback"></div>
        </div>
        <div class="form-group">
            <?=Html::submitButton($button, ['class' => 'btn btn-primary', 'name' => 'signup-button'])?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

