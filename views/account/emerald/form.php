<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */

/* @var $model \app\models\forms\LoginForm */

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use app\assets\AppAsset;

$this->title = 'Оформление заказа';

?>
<style>
    .form-group label {
        text-align: left;
    }
</style>
<div class="wrapper_form-in" style="max-width: 99%;">
    <div class="block_form w-form">
        <?php $form = ActiveForm::begin(['id' => 'order-form', 'options' => ['class' => 'order_in']]); ?>
        <input type="hidden" name="user_id" value="<?=$user_id?>">
        <?=$form->field($model, 'fullname')->textInput(['autofocus' => true, 'class' => 'logo_in w-input'])->label('Full Name')?>
        <?=$form->field($model, 'country')->textInput(['autofocus' => true, 'class' => 'logo_in w-input'])->label(Yii::t('site', 'Country'))?>
        <?=$form->field($model, 'birth_date')->textInput(['autofocus' => true, 'class' => 'logo_in w-input'])->label(Yii::t('site', 'Birth date'))?>
        <?=$form->field($model, 'phone')->textInput(['autofocus' => true, 'class' => 'logo_in w-input'])->label(Yii::t('site', 'Phone'))?>
        <?=$form->field($model, 'city')->textInput(['autofocus' => true, 'class' => 'logo_in w-input'])->label(Yii::t('site', 'City'))?>
        <?=$form->field($model, 'zip_code')->textInput(['autofocus' => true, 'class' => 'logo_in w-input'])->label(Yii::t('site', 'Zip Code'))?>
        <?=$form->field($model, 'whatsapp')->textInput(['autofocus' => true, 'class' => 'logo_in w-input'])->label(Yii::t('site', 'Whats App'))?>

        <?=Html::submitButton('Заказать', ['class' => 'btn_in w-button', 'name' => 'button'])?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<script>

</script>