<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form kartik\form\ActiveForm */
?>

    <div class="user-form">
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-2">
                <?=$form->field($model, 'number')->textInput(['maxlength' => true])?>
            </div>
        </div>
        <div class="form-group">
            <?=Html::submitButton(Yii::t('admin', 'Save'), ['class' => 'btn btn-success'])?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
