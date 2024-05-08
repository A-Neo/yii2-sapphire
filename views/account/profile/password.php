<?php

use kartik\widgets\ActiveForm;
use yii\bootstrap4\Html;
use kartik\password\PasswordInput;

/* @var $this yii\web\View */
/* @var $model app\models\forms\ProfilePasswordForm */

$this->title = Yii::t('account', 'Password change');
$this->params['breadcrumbs'][] = ['label' => Yii::t('account', 'Your profile'), 'url' => ['/pm/profile']];
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user->identity;
?>
<?=$this->render('_tabs')?>
<div class="row w-100 mt-3">
    <div class="col-xs-12 col-md-6">
        <div class="wrapper_form-balance">
            <p><?=Yii::t('site', 'Please choose your new password')?>:</p>
            <?php $form = ActiveForm::begin(['id' => 'change-password-form']); ?>
            <?=$form->field($model, 'old_password')->passwordInput()?>
            <?=$form->field($model, 'new_password')->widget(PasswordInput::class)?>
            <?=$form->field($model, 'repeat_password')->passwordInput()?>
            <div class="form-group">
                <?=Html::submitButton(Yii::t('site', 'Save'), ['class' => 'btn btn-primary'])?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
</div>