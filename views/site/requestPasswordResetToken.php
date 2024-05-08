<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */

/* @var $model \app\models\forms\PasswordResetRequestForm */

use yii\helpers\Url;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

$this->title = Yii::t('site', 'Request password reset');
$this->params['breadcrumbs'][] = ['label' => Yii::t('site', 'Login'), 'url' => ['/site/login']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wrapper_form-in">
    <h3 class="in"><?=Html::encode($this->title)?></h3>
    <p><?=Yii::t('site', 'Please fill out your login.')?></p>
    <p><?=Yii::t('site', 'A link to reset password will be sent to account email.')?></p>
    <div class="block_form w-form">
        <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>
        <?=$form->field($model, 'username')->textInput(['autofocus' => true, 'class' => 'logo_in w-input', 'placeholder' => $model->getAttributeLabel('username')])->label(false)?>
        <div class="form-group">
            <?=Html::submitButton(Yii::t('site', 'Send'), ['class' => 'btn_in w-button'])?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
