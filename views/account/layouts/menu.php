<?php

use yii\helpers\Url;

/**
 * @var $hide bool
 */
$hide = $hide??false;
?>
<nav role="navigation" class="nav_menu w-nav-menu" id="w-nav-menu" <?=$hide ? 'style="left:-32vh; width: 32vh; position: absolute;"' : ''?> >
    <?php if(Yii::$app->user->can('admin,moderator')): ?>
        <a href="<?=Url::to(['/cp/default/index'])?>" class="link_balance w-nav-link text-danger"><?=Yii::t('account', 'Admin panel')?></a>
    <?php endif ?>
    <a href="<?=Url::to(['/pm/default/index'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'Dashboard')?></a>
    <a href="<?=Url::to(['/pm/balance/index'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'Balance')?></a>
    <a href="<?=Url::to(['/pm/partner/index'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'Personally invited')?></a>
    <a href="<?=Url::to(['/pm/network/index'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'My network Sapphire')?></a>
	<a href="<?=Url::to(['/pm/travel'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'My network Travel')?></a>
    <a href="<?=Url::to(['/pm/emerald/index'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'Emerald Health')?></a>
    <a href="<?=Url::to(['/pm/tour/index'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'Sapphire tour')?></a>
    <a href="<?=Url::to(['/pm/shop/index'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'Shop')?></a>
    <a href="<?=Url::to(['/pm/profile/index'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'Profile')?></a>
    <a href="<?=Url::to(['/pm/news/index'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'News')?></a>
    <a href="<?=Url::to(['/pm/profile/link'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'Referral link')?></a>
    <a href="<?=Url::to(['/site/logout'])?>" class="link_balance w-nav-link"><?=Yii::t('account', 'Logout')?></a>
</nav>