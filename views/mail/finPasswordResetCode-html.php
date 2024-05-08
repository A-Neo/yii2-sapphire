<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $user app\models\User */

?>
<div class="password-reset">
    <p><?=Yii::t('site','Hello')?> <?= Html::encode($user->username) ?>,</p>

    <?=Yii::t('site','Yours financial password reset code')?>:

    <p><?= $user->password_reset_token ?></p>
</div>
