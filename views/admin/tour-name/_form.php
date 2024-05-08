<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TourName */
/* @var $form yii\widgets\ActiveForm */
$languages = Yii::$app->languagesDispatcher->languages;
?>

<div class="tour-name-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-12 col-md-4">
            <?=$form->field($model, 'status')->checkbox()?>
            <?=$form->field($model, 'price')->textInput(['type' => 'number', 'min' => 3, 'step' => 1])?>
        </div>
        <div class="col-12 col-md-8">
            <?php foreach($languages as $code): ?>
                <?=$form->field($model, 'name[' . $code . ']')->textInput()->label($model->getAttributeLabel('name') . ' (' . $code . ')');?>
            <?php endforeach ?>
        </div>
    </div>

    <div class="form-group">
        <?=Html::submitButton(Yii::t('admin', 'Save'), ['class' => 'btn btn-success'])?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
