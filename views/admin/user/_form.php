<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form kartik\form\ActiveForm */
$s = $model->id == Yii::$app->user->id;
?>

    <div class="user-form">
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-4">
                <?=$form->field($model, 'username')->textInput(['maxlength' => true, 'readonly' => $s, 'disabled' => $s])?>
            </div>
            <div class="col-2">
                <?=$form->field($model, 'password')->textInput(['maxlength' => true])?>
            </div>
            <div class="col-2">
                <?=$form->field($model, 'fin_password_t')->textInput(['maxlength' => true])?>
            </div>
            <div class="col-4">
                <?=$form->field($model, 'full_name')->textInput(['maxlength' => true])?>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <?=$form->field($model, 'email')->textInput(['maxlength' => true])?>
            </div>
            <div class="col-4">
                <div class="row">
                    <div class="col-6">
                        <?=$form->field($model, 'role')->dropDownList($model->getRolesList())?>
                        <?=$form->field($model, 'referrer_name')->textInput([
                            'maxlength' => true,
                            'value'     => $model->referrer ? $model->referrer->username : null,
                            'readonly'  => $model->id == 1,
                            'disabled'  => $model->id == 1,
                        ])?>
                    </div>
                    <div class="col-6">
                        <div class=" <?=$model->role == 'moderator' ? '' : 'd-none'?>" id="permissions">
                            <?=$form->field($model, 'permissions')->checkboxList($model->getPermissionsList())?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <?php
                $countries = include_once ROOT_DIR . '/config/' . substr(Yii::$app->language, 0, 2) . '/countries.php';
                $data = array_combine(array_column($countries, 'name'), array_column($countries, 'name'));
                ?>
                <?=$form->field($model, 'country')->dropDownList($countries, ['prompt' => '']);?>
                <?=$form->field($model, 'phone')->textInput(['maxlength' => true])?>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <?=$form->field($model, 'status')->dropDownList($model->getStatusesList())?>
            </div>
            <div class="col-2">
                <?=$form->field($model, 'wallet')->textInput(['maxlength' => true])?>
            </div>
            <div class="col-2">
                <?=$form->field($model, 'wallet_perfect')->textInput(['maxlength' => true])?>
            </div>
            <div class="col-2">
                <?=$form->field($model, 'wallet_tether')->textInput(['maxlength' => true])?>
            </div>
            <div class="col-2">
                <?=$form->field($model, 'wallet_banki_rf')->textInput(['maxlength' => true])?>
            </div>
            <div class="col-2">
                <?=$form->field($model, 'wallet_dc')->textInput(['maxlength' => true])?>
            </div>
            <div class="col-2">
                <?= $form->field($model, 'balance_travel', ['addon' => ['append' => ['content' => '$']]])->textInput(['type' => 'number', 'step' => 'any']);?>
            </div>
            <div class="col-4">
                <?=$form->field($model, 'birth_date')->widget(DatePicker::class, [
                    'pluginOptions' => [
                        'format'         => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                    ],
                ]);?>
            </div>
        </div>
        <div class="form-group">
            <?=Html::submitButton(Yii::t('admin', 'Save'), ['class' => 'btn btn-success'])?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
<?php
$this->registerJs(<<<JS
    $('#user-role').on('change keyup paste', function(){
       if($(this).val() == 'moderator')
           $('#permissions').removeClass('d-none')
       else
           $('#permissions').addClass('d-none')
    });
JS
);
