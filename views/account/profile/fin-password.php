<?php

use kartik\widgets\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\forms\ProfileFinPasswordForm */

$this->title = Yii::t('account', 'Financial password change');
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
            <?php if(!empty($model->user->fin_password)): ?>
                <?=$form->field($model, 'old_password')->passwordInput()?>
            <?php endif ?>
            <?=$form->field($model, 'new_password')->passwordInput()?>
            <?=$form->field($model, 'repeat_password')->passwordInput()?>
            <?php if(!empty($model->user->fin_password)): ?>
                <?=$form->field($model, 'code')->textInput()->hint(
                    Yii::t('account', 'Confirmation code was sent to your email') . '<br/>' .
                    Html::a(Yii::t('account', 'Send confirmation code to your email again'), ['/pm/profile/fin-password-code'])
                )?>
            <?php endif ?>
            <div class="form-group">
                <?=Html::submitButton(Yii::t('site', 'Save'), ['class' => 'btn btn-primary'])?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>