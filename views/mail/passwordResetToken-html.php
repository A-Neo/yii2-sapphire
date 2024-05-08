<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $user app\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
<div class="password-reset">
    <p><?=Yii::t('site','Hello')?> <?= Html::encode($user->username) ?>,</p>

    <p><?=Yii::t('site','Follow the link below to reset your password')?>:</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>
