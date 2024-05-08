<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use kartik\editors\Summernote;

/* @var $this yii\web\View */
/* @var $model app\models\Page */
/* @var $form yii\bootstrap4\ActiveForm */
$languages=Yii::$app->languagesDispatcher->languages;
?>

<div class="page-form">

    <?php $form = ActiveForm::begin(['validateOnBlur' => false]); ?>
    <div class="row">
        <div class="col-12 col-md-4">
            <?=$form->field($model, 'slug')->textInput(['maxlength' => true])?>

            <?=$form->field($model, 'status')->checkbox()?>
        </div>
        <div class="col-12 col-md-8">
            <?php foreach ($languages as $code): ?>
                <?= $form->field($model, 'title[' . $code . ']')->textInput()->label($model->getAttributeLabel('title') . ' (' . $code . ')'); ?>
            <?php endforeach ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-4">
            <?php foreach ($languages as $code): ?>
                <?= $form->field($model, 'seo_title[' . $code . ']')->textInput()->label($model->getAttributeLabel('seo_title') . ' (' . $code . ')'); ?>
            <?php endforeach ?>
        </div>
        <div class="col-12 col-md-4">
            <?php foreach ($languages as $code): ?>
                <?= $form->field($model, 'seo_description[' . $code . ']')->textInput()->label($model->getAttributeLabel('seo_description') . ' (' . $code . ')'); ?>
            <?php endforeach ?>
        </div>
        <div class="col-12 col-md-4">
            <?php foreach ($languages as $code): ?>
                <?= $form->field($model, 'seo_keywords[' . $code . ']')->textInput()->label($model->getAttributeLabel('seo_keywords') . ' (' . $code . ')'); ?>
            <?php endforeach ?>
        </div>
    </div>
    <?php foreach ($languages as $code): ?>
        <?=$form->field($model, 'text[' . $code . ']')->widget(Summernote::class)->label($model->getAttributeLabel('text') . ' (' . $code . ')')?>
    <?php endforeach ?>


    <div class="form-group">
        <?=Html::submitButton(Yii::t('admin', 'Save'), ['class' => 'btn btn-success'])?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
