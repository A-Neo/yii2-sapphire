<?php

use app\models\Tour;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use kartik\widgets\FileInput;
use yii\helpers\ArrayHelper;

/**
 *
 * @var $model          \app\models\Tour
 * @var $availableTours \app\models\TourName[]
 */
$readonly = $model->status == Tour::STATUS_CONFIRMED;
?>
<div class="row w-100">
    <div class="col-xs-12 col-sm-6 p-3">
        <div class="inform_tour _2 w-inline-block w-100 ">
            <?php $form = ActiveForm::begin(['id' => 'change-wallet-form', 'options' => ['enctype' => 'multipart/form-data'], 'action' => ['/pm/tour/index', 'id' => $model->id]]); ?>
            <?=$model->isNewRecord ? '' : $form->field($model, 'updated_at')->textInput(['readonly' => true, 'disabled' => true, 'value' => Yii::$app->formatter->asDatetime($model->updated_at)])?>
            <?=$form->field($model, 'status')->textInput(['readonly' => true, 'disabled' => true, 'value' => $model->statusName])?>
            <?php if($readonly): ?>
                <?=$form->field($model, 'tour_name_id')->textInput(['readonly' => $readonly, 'disabled' => $readonly, 'value' => $model->tourName->t_name])?>
            <?php else: ?>
                <?=$form->field($model, 'tour_name_id')->dropdownList(ArrayHelper::map($availableTours, 'id', 't_name'), ['prompt' => 'Select tour'])?>
            <?php endif ?>
            <?=$form->field($model, 'whatsapp')->textInput(['readonly' => $readonly, 'disabled' => $readonly])?>
            <?=$form->field($model, 'number')->textInput(['readonly' => $readonly, 'disabled' => $readonly])?>
            <?php if(!$readonly): ?>
                <?=$form->field($model, 'passport', ['enableClientValidation' => false])->widget(FileInput::class, [
                    'options'       => ['accept' => 'image/*'],
                    'pluginOptions' => [
                        'showPreview' => false,
                    ],
                ])?>
            <?php endif ?>
            <?php if($model->passport): ?>
                <?=Html::img(['/pm/tour/passport', 'id' => $model->id], ['style' => 'width:100%'])?>
            <?php endif ?>
            <?php if(!$readonly): ?>
                <div class="form-group">
                    <?=Html::submitButton(Yii::t('site', 'Save'), ['class' => 'btn btn-primary'])?>
                </div>
            <?php endif ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
