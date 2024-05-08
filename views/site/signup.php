<?php

/**
 * @var $this     yii\web\View
 * @var $form     yii\bootstrap4\ActiveForm
 * @var $model    \app\models\forms\SignupForm
 * @var $referrer \app\models\User
 * @var $id       int|null
 */

use yii\bootstrap4\Html;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\password\PasswordInput;
use app\models\User;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\View;
use app\assets\AppAsset;

$countries = include_once ROOT_DIR . '/config/' . substr(Yii::$app->language, 0, 2) . '/countries.php';
$data = array_combine(array_column($countries, 'name'), array_column($countries, 'name'));
$countries = array_combine(array_column($countries, 'name'), array_values($countries));
$url = AppAsset::image('../flags/', false);
$format = <<< SCRIPT
function format(state) {
    if (!state.id) return state.text; // optgroup
    src = '$url' +  $(state.element).attr('alpha2') + '.png'
    return '<img class="flag" src="' + src + '" height="24"/> ' + state.text;
}
SCRIPT;
$escape = new JsExpression("function(m) { return m; }");
$this->registerJs($format, View::POS_HEAD);
$this->title = Yii::t('site', 'Registration');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wrapper_form-in wrapper_form-in-reg">
    <h3 class="in"><?=Yii::t('site', 'Signup')?></h3>
    <div class="block_form w-form">
        <?php $form = ActiveForm::begin(['id' => 'form-signup', 'action' => Url::to(['/site/signup', 'slug' => $referrer ? $referrer->username : ''])]); ?>
        <?=$form->field($model, 'full_name')->textInput(['autofocus' => true, 'style' => 'max-width:310px;', 'class' => 'reg_id w-input'])->label(Yii::t('site', 'Full name'))?>
        <?=$form->field($model, 'username')->textInput(['autofocus' => true, 'style' => 'max-width:310px;', 'class' => 'reg_id w-input',])->label(Yii::t('site', 'Username'))?>
        <?=$form->field($model, 'email')->textInput(['type' => 'email', 'style' => 'max-width:310px;', 'class' => 'reg_id w-input'])->label(Yii::t('site', 'Email'))?>

        <?=$form->field($model, 'country')->widget(Select2::class, [
            'language'      => Yii::$app->language,
            'options'       => [
                'placeholder' => Yii::t('site', 'Select a country ...'),
                'class'       => 'reg_id w-select form-control',
                'options'     => $countries,
            ],
            'pluginOptions' => [
                'templateResult'    => new JsExpression('format'),
                'templateSelection' => new JsExpression('format'),
                'escapeMarkup'      => $escape,
            ],
            'data'          => $data,
        ])->label(Yii::t('site', 'Select a country ...'))?>

        <?=$form->field($model, 'phone')->textInput(['autofocus' => true, 'style' => 'max-width:310px;', 'class' => 'reg_id w-input'])->label(Yii::t('site', 'Phone'))?>
        <?=$form->field($model, 'password')->widget(PasswordInput::class, ['options' => ['class' => 'reg_password-confirmation w-input']])->label(Yii::t('site', 'Password'))?>
        <?=$form->field($model, 'repeat_password')->widget(PasswordInput::class, ['options' => ['class' => 'reg_password-confirmation w-input']])->label(Yii::t('site', 'Repeat password'))?>
        <?=$form->field($model, 'referrer')->textInput(['autofocus' => true, 'style' => 'max-width:310px;', 'class' => 'reg_id w-input'])->label(Yii::t('site', 'Referrer'))?>
        <div class="form-group">
            <?=Html::submitButton(Yii::t('site', 'Signup'), ['class' => 'btn_in w-button', 'name' => 'signup-button'])?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<div class="login-new-account">
    <div class="text_akk"><?=Yii::t('site', 'Have an account yet?')?> <a href="<?=Url::to(['site/login'])?>" class="registration"><?=Yii::t('site', 'Login') ?></a></div>
    <!--<a href="<?=Url::to(['site/resend-verification-email'])?>"><?=Yii::t('site', 'Need new verification email?')?></a>-->
</div>
