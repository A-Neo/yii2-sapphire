<?php
/** @var \yii\web\View $this */

/** @var string $content */

use app\assets\AppAsset;

$code = substr(Yii::$app->language, 0, 2);
?>
<div class="language">
    <div class="wrapp_language">
        <div class="wrapp_arrow-tr">
            <div class="wrapp_arrow"><img src="<?=AppAsset::image('6261a90e395ab074a04237ef_down-arrow.png')?>" loading="lazy" alt class="arrow"></div>
            <a href="#" class="tr w-inline-block"><img src="<?=AppAsset::image('flags/' . $code . '.png')?>" loading="lazy" alt class="tr_flag">
                <div class="tr_name"><?=strtoupper($code)?></div>
            </a>
        </div>
        <?php foreach(Yii::$app->languagesDispatcher->languages as $language): ?>
            <?php if($language == $code) continue; ?>
            <a style="display:none" href="?lang=<?=$language?>" class="tr w-inline-block"><img src="<?=AppAsset::image('flags/' . $language . '.png')?>" loading="lazy" alt class="tr_flag">
                <div class="tr_name"><?=strtoupper($language)?></div>
            </a>
        <?php endforeach ?>
    </div>
</div>