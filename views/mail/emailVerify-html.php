<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $user app\models\User */

$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verification_token]);
?>
<div class="verify-email">
    <p><?=Yii::t('site','Hello')?> <?= Html::encode($user->username) ?>,</p>

    <p><?=Yii::t('site','Follow the link below to verify your email')?>:</p>

    <p><?= Html::a(Html::encode($verifyLink), $verifyLink) ?></p>
</div>
