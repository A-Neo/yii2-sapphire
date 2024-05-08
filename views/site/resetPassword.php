<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */

/* @var $model \app\models\forms\ResetPasswordForm */

use yii\bootstrap4\Html;
use kartik\widgets\ActiveForm;
use kartik\password\PasswordInput;

$this->title = Yii::t('site', 'Reset password');
$this->params['breadcrumbs'][] = ['label' => Yii::t('site', 'Login'), 'url' => ['/site/login']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wrapper_form-in">
    <h3 class="in"><?=Html::encode($this->title)?></h3>
    <p><?=Yii::t('site', 'Please choose your new password')?>:</p>
    <div class="block_form w-form">
        <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
        <?=$form->field($model, 'password')->widget(PasswordInput::class, ['class' => 'form-control password_in w-input', 'options' => ['placeholder' => $model->getAttributeLabel('password')]])->label(false)?>
        <div class="form-group">
            <?=Html::submitButton(Yii::t('site', 'Save'), ['class' => 'btn_in w-button'])?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
