<?php
/** @var \yii\web\View $this */

/** @var string $content */

use app\assets\AppAsset;
use yii\bootstrap4\Html;
use app\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage(); ?>
    <!DOCTYPE html>
    <html lang="<?=Yii::$app->language?>">
    <head>
	
		<!-- Chatra {literal} -->
<script>
    (function(d, w, c) {
        w.ChatraID = 'dJQc5iTrDpnb9nWmD';
        var s = d.createElement('script');
        w[c] = w[c] || function() {
            (w[c].q = w[c].q || []).push(arguments);
        };
        s.async = true;
        s.src = 'https://call.chatra.io/chatra.js';
        if (d.head) d.head.appendChild(s);
    })(document, window, 'Chatra');
</script>
<!-- /Chatra {/literal} -->
        <meta charset="<?=Yii::$app->charset?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?=Html::encode($this->title)?></title>
        <?php $this->registerCsrfMetaTags() ?>
        <?php $this->head() ?>
    </head>
    <body class="body">
    <?php $this->beginBody() ?>
    <div class="section-in wf-section">
        <div class="block_exit-language">
            <?=$this->render('language')?>
        </div>
        <div class="block_logo-form">
            <img src="<?=AppAsset::image('626178820fa83d20781dc9bb_logo-out.png')?>" loading="lazy" alt class="brand">
            <?=Alert::widget()?>
            <?=$content?>
        </div>
        <div class="wrapp_soc-icon">
            <?php $url = Yii::$app->settings->get('social', 'telegram', '#'); ?>
            <?php if(!empty($url) && $url != '#'): ?>
                <a href="<?=$url?>" target="_blank" class="link_tw w-inline-block">
                    <img src="<?=AppAsset::image('telegram.png')?>" loading="lazy" width="40" class="img_tl">
                </a>
            <?php endif ?>
            <?php $url = Yii::$app->settings->get('social', 'instagram', '#'); ?>
            <?php if(!empty($url) && $url != '#'): ?>
                <a href="<?=$url?>" target="_blank" class="link_tw w-inline-block">
                    <img src="<?=AppAsset::image('instagram.png')?>" loading="lazy" width="40" class="img_in">
                </a>
            <?php endif ?>
            <?php $url = Yii::$app->settings->get('social', 'youtube', '#'); ?>
            <?php if(!empty($url) && $url != '#'): ?>
                <a href="<?=$url?>" target="_blank" class="link_tw w-inline-block">
                    <img src="<?=AppAsset::image('youtube.png')?>" loading="lazy" width="40" class="img_in">
                </a>
            <?php endif ?>
        </div>
    </div>
    <?php $this->endBody() ?>
	
	
    </body>
    </html>
<?php $this->endPage();