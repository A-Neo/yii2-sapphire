<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use app\models\enumerables\SettingType;
use yii2mod\settings\models\enumerables\SettingStatus;
use app\components\LanguageSelector;

/* @var $this \yii\web\View */
/* @var $model app\models\SettingModel */
?>

<div class="setting-form">

    <?php $form = ActiveForm::begin(['validateOnBlur' => false]); ?>
    <div class="row">
        <div class="col-6 col-md-3">
            <div class="form-group">
                <label><?=$model->getAttributeLabel('key')?></label>
                <div class="form-control readonly" readonly><?=$model->key?></div>
                <?php $tr = Yii::t('setting', $model->key);
                echo $tr != $model->key ? $tr : ''; ?>
            </div>
            <?php if($model->system): ?>
                <label><?=$model->getAttributeLabel('status')?></label>
                <div class="form-control readonly" readonly><?=SettingStatus::listData()[$model->status]?></div>
            <?php else: ?>
                <?=$form->field($model, 'status')->dropDownList(SettingStatus::listData());?>
            <?php endif ?>
            <div class="form-group">
                <label><?=$model->getAttributeLabel('section')?></label>
                <div class="form-control readonly" readonly><?=$model->section?></div>
            </div>
            <div class="form-group">
                <label><?=$model->getAttributeLabel('type')?></label>
                <div class="form-control readonly" readonly><?=$model->type ? SettingType::listData()[$model->type] : ''?></div>
            </div>
        </div>
        <div class="col-6 col-md-9">
            <?php switch($model->type):
                case SettingType::BOOLEAN_TYPE: ?>
                    <?=$form->field($model, 'value')->checkbox();?>
                    <?php break; ?>
                <?php case SettingType::INTEGER_TYPE: ?>
                    <?=$form->field($model, 'value')->textInput(['type' => 'number', 'step' => '1']);?>
                    <?php break; ?>
                <?php case SettingType::FLOAT_TYPE: ?>
                    <?=$form->field($model, 'value')->textInput(['type' => 'number', 'step' => 'any']);?>
                    <?php break; ?>
                    <?php break; ?>
                <?php default: ?>
                    <?=$form->field($model, 'value')->textarea(['rows' => 5]);?>
                <?php endswitch ?>
            <div class="text-description"><?=$model->description?></div>
        </div>
    </div>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? Yii::t('yii2mod.settings', 'Create') : Yii::t('yii2mod.settings', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']); ?>
        <?php echo Html::a(Yii::t('yii2mod.settings', 'Go Back'), ['index'], ['class' => 'btn btn-default']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
