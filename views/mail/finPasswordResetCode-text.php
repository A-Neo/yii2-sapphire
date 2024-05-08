<?php

/* @var $this yii\web\View */
/* @var $user app\models\User */

?>
<?=Yii::t('site', 'Hello')?> <?=$user->username?>,

<?=Yii::t('site', 'Yours financial password reset code')?>:

<?=$user->password_reset_token?>
