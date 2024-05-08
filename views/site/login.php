<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */

/* @var $model \app\models\forms\LoginForm */

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use app\assets\AppAsset;

$this->title = Yii::t('site', 'Login');
$this->params['breadcrumbs'][] = $this->title;
$ilock = AppAsset::image('62619b61ef0b1e290d43a6b8_padlock.png');
$unlock = AppAsset::image('62619b611a0d610eee6b8814_unlock.png');
$lock = <<<HTML
<div class="wrapp_icon-lock">
    <a data-w-id="c9287360-6601-6154-e8bb-695c896a302c" href="#" class="link_icon-unlock w-inline-block" style="display: flex;">
        <img src="{$ilock}" loading="lazy" width="20" alt="" class="unlock">
    </a>
    <a data-w-id="5983a5f2-aae8-5761-9d45-f855a5f2c4a0" href="#" class="link_icon-padlock w-inline-block" style="display: none;">
        <img src="{$unlock}" loading="lazy" width="20" alt="" class="padlock">
    </a>
</div>
HTML;

?>
<div class="wrapper_form-in">
    <!--<h3 class="in"><?=Html::encode($this->title)?></h3>-->
    <h3 class="in"><?=Yii::t('site', 'Login') ?></h3>
    <div class="block_form w-form">
        <?php $form = ActiveForm::begin(['id' => 'login-form', 'options' => ['class' => 'form_in']]); ?>
        
        <?=$form->field($model, 'username')->textInput(['autofocus' => true, 'class' => 'logo_in w-input'])->label(Yii::t('site', 'Username'))?>
        <?=$form->field($model, 'password')->passwordInput(['class' => 'password_in w-input', 'id' => 'password-input'])->label(Yii::t('site', 'Password'))?>
        <div class="form-group show-group-password">
            <?=Html::checkbox('showPassword', false, ['class' => 'password-checkbox', 'id' => 'password-checkbox', 'label' => Yii::t('site', 'Show password')])?>
        </div>
        <a href="<?=Url::to(['site/request-password-reset'])?>" class="btn_password-forgut w-button mt-0"><?=Yii::t('site', 'Forgot your password?')?></a>
        <?=$form->field($model, 'rememberMe')->checkbox()?>
       
        
        <?=Html::submitButton(Yii::t('site', 'Login'), ['class' => 'btn_in w-button', 'name' => 'login-button'])?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<div class="login-new-account">
    <div class="text_akk"><?=Yii::t('site', 'Don\'t have an account yet?')?> <a href="<?=Url::to(['site/signup'])?>" class="registration"><?=Yii::t('site', 'Create')?></a></div>
    <!--<a href="<?=Url::to(['site/resend-verification-email'])?>"><?=Yii::t('site', 'Need new verification email?')?></a>-->
</div>
<script>
    document.getElementById('password-checkbox').addEventListener('change', function () {
        var passwordInput = document.getElementById('password-input');
        if (this.checked) {
            passwordInput.type = 'text';
        } else {
            passwordInput.type = 'password';
        }
    });
</script>