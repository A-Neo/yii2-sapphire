<?php

/* @var $this yii\web\View */
/* @var $user app\models\User */

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
?>
<?=Yii::t('site','Hello')?> <?= $user->username ?>,

<?=Yii::t('site','Follow the link below to verify your email')?>:

<?= $verifyLink ?>
