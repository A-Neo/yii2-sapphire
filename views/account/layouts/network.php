<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\AppAsset;
use app\widgets\Alert;
use app\models\Balance;

$user = Yii::$app->user->identity;
AppAsset::register($this);
$hasPoints = Balance::find()->where(['to_user_id' => $user->id, 'status' => Balance::STATUS_WAITING, 'comment' => ''])->exists();
$this->registerJs(<<<JS
function checkComment(){
    var comment = $('#tx-comment').val();
    comment=comment.trim();
    if(comment) {
        $('#send-request').removeAttr('disabled').removeClass('disabled');
    } else {
        $('#send-request').attr('disabled','disabled').addClass('disabled');
    }
}
checkComment();
$('#send-request').on('click', function (e){
  e.preventDefault();
  if($('#send-request').hasClass('disabled')) {
      return false;
  }
  $('#request-form')[0].submit();
  return false;
});
$('#tx-comment').on('change keyup paste click', function (){
    checkComment();    
});
JS
);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language?>">
<head>
    <meta charset="<?=Yii::$app->charset?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=Html::encode($this->title)?></title>
    <?php $this->registerCsrfMetaTags() ?>
    <?php $this->head() ?>
</head>
<body class="body-3">
<?php $this->beginBody() ?>
<div class="all">
    <div class="section_hear wf-section">
        <div class="block_menu-main">
            <div class="menu-button w-nav-button d-block p-0" id="menu-ctl" aria-label="menu" role="button" tabindex="0" aria-controls="w-nav-overlay-0" aria-haspopup="menu" aria-expanded="false">
                <div class="icon w-icon-nav-menu fa-2x"></div>
            </div>
            <a href="#" class="main"><?=Yii::t('account', 'Main')?></a></div>
        <div class="block_btn-exit">
            <div class="div-block-7">
                <?php if($hasPoints): ?>
                    <a href="#" class="btn_balance w-button" data-toggle="modal" data-target="#sapphire-modal" title="<?=Yii::t('account', 'Sapphire points waiting for request')?>"><?=Yii::t('account', 'Request')?></a>
                <?php endif ?>
                <a href="/pm/balance" class="btn_balance w-button"><?=Yii::t('account', 'Balance')?> <span class="span"><?=number_format($user->balance - $user->accumulation, 2)?></span> $</a>
                <a href="/pm/profile" class="btn_profil w-button"><?=Yii::t('account', 'Profile')?></a>
                <?php if(Yii::$app->session->has('back-to-admin')): ?>
                    <a href="/pm/default/back" class="btn_balance w-button"><?=Yii::t('account', 'Back to Admin')?></a>
                <?php endif ?>
            </div>
            <!--<a href="<?=Url::to(['/site/logout'])?>" class="w-inline-block"><img src="<?=AppAsset::image('62617f1c0fa5845b3a39f86c_logout.png');?>" loading="lazy" width="32" alt class="image-12"></a>-->
        </div>
    </div>
    <?=$this->render('sapphire-modal');?>
    <div class="wrapp_block-content">
        <?=$this->render('menu', ['hide' => true]);?>
        <?=Alert::widget()?>
        <?=$content?>
    </div>
</div>
<div class="wrapp_soc-icon _2">
    <?php $url = Yii::$app->settings->get('social', 'telegram', '#'); ?>
    <?php if(!empty($url) && $url != '#'): ?>
        <a href="<?=$url?>" target="_blank" class="link-in w-inline-block">
            <img src="<?=AppAsset::image('telegram2.png')?>" loading="lazy" width="40" class="image_tl">
        </a>
    <?php endif ?>
    <?php $url = Yii::$app->settings->get('social', 'instagram', '#'); ?>
    <?php if(!empty($url) && $url != '#'): ?>
        <a href="<?=$url?>" target="_blank" class="link-in w-inline-block">
            <img src="<?=AppAsset::image('instagram2.png')?>" loading="lazy" width="40" class="image_tl">
        </a>
    <?php endif ?>
    <?php $url = Yii::$app->settings->get('social', 'youtube', '#'); ?>
    <?php if(!empty($url) && $url != '#'): ?>
        <a href="<?=$url?>" target="_blank" class="link-in w-inline-block">
            <img src="<?=AppAsset::image('youtube2.png')?>" loading="lazy" width="40" class="image_tl">
        </a>
    <?php endif ?>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

