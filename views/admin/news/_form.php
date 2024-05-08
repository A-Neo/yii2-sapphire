<?php

use kartik\editors\Summernote;
use kartik\datetime\DateTimePicker;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\News */
/* @var $form ActiveForm */
$languages = Yii::$app->languagesDispatcher->languages;
\yii2mod\editable\bundles\EditableDateTimePickerAsset::register($this);
$this->registerJs("$('#datetimepickerinput').datetimepicker({autoclose:true,fontAwesome:true,format:'dd-mm-yyyy hh:ii:00',locale:'" . substr(Yii::$app->language, 0, 2) . "'});");
?>

<div class="news - form">

    <?php $form = ActiveForm::begin(['validateOnBlur' => false]); ?>
    <div class="row">
        <div class="col-12 col-md-4">
            <?=$form->field($model, 'published_at')->widget(DateTimePicker::class, [
                'type'          => DateTimePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format'    => 'dd-mm-yyyy HH:ii',
                ],
            ])?>
        </div>
        <div class="col-12 col-md-8">
            <?php foreach($languages as $code): ?>
                <?=$form->field($model, 'title[' . $code . ']')->textInput()->label($model->getAttributeLabel('title') . ' (' . $code . ')');?>
            <?php endforeach ?>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-4">
            <?php foreach($languages as $code): ?>
                <?=$form->field($model, 'seo_title[' . $code . ']')->textInput()->label($model->getAttributeLabel('seo_title') . ' (' . $code . ')');?>
            <?php endforeach ?>
        </div>
        <div class="col-12 col-md-4">
            <?php foreach($languages as $code): ?>
                <?=$form->field($model, 'seo_description[' . $code . ']')->textInput()->label($model->getAttributeLabel('seo_description') . ' (' . $code . ')');?>
            <?php endforeach ?>
        </div>
        <div class="col-12 col-md-4">
            <?php foreach($languages as $code): ?>
                <?=$form->field($model, 'seo_keywords[' . $code . ']')->textInput()->label($model->getAttributeLabel('seo_keywords') . ' (' . $code . ')');?>
            <?php endforeach ?>
        </div>
    </div>
    <?php foreach($languages as $code): ?>
        <?=$form->field($model, 'short[' . $code . ']')->widget(Summernote::class, ['pluginOptions' => ['height' => '150']])->label($model->getAttributeLabel('short') . ' (' . $code . ')')?>
    <?php endforeach ?>
    <?php foreach($languages as $code): ?>
        <?=$form->field($model, 'text[' . $code . ']')->widget(Summernote::class)->label($model->getAttributeLabel('text') . ' (' . $code . ')')?>
    <?php endforeach ?>
    <div class="form - group">
        <?=Html::submitButton(Yii::t('admin', 'Save'), ['class' => 'btn btn-success'])?>
    </div>


    <?php ActiveForm::end(); ?>

</div>
