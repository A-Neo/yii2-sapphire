<?php

use kartik\widgets\ActiveForm;
use yii\bootstrap4\Html;
use kartik\widgets\DatePicker;
use kartik\widgets\FileInput;
use app\assets\AppAsset;
use yii\web\JsExpression;
use kartik\widgets\Select2;
use app\components\View;

/* @var $this yii\web\View */
/* @var $model app\models\forms\ProfileForm */

$this->title = Yii::t('account', 'Profile setup');
$this->params['breadcrumbs'][] = ['label' => Yii::t('account', 'Your profile'), 'url' => ['/pm/profile']];
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user->identity;
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

?>
<?=$this->render('_tabs')?>
<div class="row w-100 mt-3">
    <div class="col-xs-12 col-md-6">
        <div class="wrapper_form-balance">
            <?php $form = ActiveForm::begin(['id' => 'change-wallet-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>
            <?=$form->field($model, 'wallet')->textInput(['readonly' => !empty($model->wallet), 'disabled' => !empty($model->wallet), 'placeholder' => Yii::t('account', 'Please enter your wallet address')])?>
            <?=$form->field($model, 'wallet_perfect')->textInput(['readonly' => !empty($model->wallet_perfect), 'disabled' => !empty($model->wallet_perfect), 'placeholder' => Yii::t('account', 'Please enter your wallet address')])?>
            <?=$form->field($model, 'wallet_tether')->textInput(['readonly' => !empty($model->wallet_tether), 'disabled' => !empty($model->wallet_tether), 'placeholder' => Yii::t('account', 'Please enter your wallet address')])?>
            <?=$form->field($model, 'wallet_banki_rf')->textInput(['readonly' => !empty($model->wallet_banki_rf), 'disabled' => !empty($model->wallet_banki_rf), 'placeholder' => Yii::t('account', 'Please enter your wallet address')])?>
            <?=$form->field($model, 'wallet_dc')->textInput(['readonly' => !empty($model->wallet_dc), 'disabled' => !empty($model->wallet_dc), 'placeholder' => Yii::t('account', 'Please enter your wallet address')])?>
            <?=$form->field($model, 'phone')->textInput(['readonly' => !empty($model->phone), 'disabled' => !empty($model->phone), 'placeholder' => Yii::t('account', 'Please enter your phone')])?>
            <?=$form->field($model, 'full_name')->textInput(['readonly' => !empty($model->full_name), 'disabled' => !empty($model->full_name), 'placeholder' => Yii::t('account', 'Please enter your full name')])?>
            <?=$form->field($model, 'country')->widget(Select2::class, [
                'language'      => Yii::$app->language,
                'options'       => [
                    'placeholder' => Yii::t('site', 'Select a country ...'),
                    'class'       => 'reg_id w-select form-control',
                    'options'     => $countries,
                    'readonly'    => !empty($model->country),
                    'disabled'    => !empty($model->country),
                ],
                'pluginOptions' => [
                    'templateResult'    => new JsExpression('format'),
                    'templateSelection' => new JsExpression('format'),
                    'escapeMarkup'      => $escape,
                ],
                'data'          => $data,
            ])?>
            <?=$form->field($model, 'birth_date')->widget(DatePicker::class, [
                'pluginOptions' => [
                    'format'         => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                ],
                'options'       => [
                    'readonly'    => !empty($model->wallet), 'disabled' => !empty($model->birth_date),
                    'placeholder' => Yii::t('account', 'Please enter your birth date'),
                ],
            ]);?>
            <?=$form->field($model, 'avatar')->widget(FileInput::class, [
                'options'       => ['accept' => 'image/*'],
                'pluginOptions' => [
                    'showPreview' => false,
                ],
            ])?>
            <div class="form-group">
                <?=Html::submitButton(Yii::t('site', 'Save'), ['class' => 'btn btn-primary'])?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>