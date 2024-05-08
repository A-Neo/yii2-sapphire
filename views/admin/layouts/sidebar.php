<?php

use yii\helpers\Url;
use app\models\Balance;

?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="<?=$assetDir?>/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"><?=Yii::t('account', 'Admin panel')?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?=Url::to(['/pm/profile/avatar'])?>" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="/pm/profile" class="d-block"><?=Yii::$app->user->identity->username?></a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <!-- href be escaped -->
        <!-- <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div> -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <?php
            $hasPointsRequests = Balance::find()->where(['status' => Balance::STATUS_WAITING])->exists();
            echo \hail812\adminlte\widgets\Menu::widget([
                'items' => [
                    ['label' => Yii::t('admin', 'Account'), 'url' => ['/pm/default/index'], 'icon' => 'tachometer-alt'],
                    ['label' => Yii::t('admin', 'Management'), 'header' => true],
                    ['label' => Yii::t('admin', 'Dashboard'), 'url' => ['/cp/default/index'], 'icon' => 'tachometer-alt'],
                    ['label' => Yii::t('admin', 'Users'), 'url' => ['/cp/user/index'], 'icon' => 'users', 'visible' => Yii::$app->user->can('user')],
                    ['label' => Yii::t('admin', 'Activations'), 'url' => ['/cp/activation/index'], 'icon' => 'clipboard-list', 'visible' => Yii::$app->user->can('activation')],
                    ['label' => Yii::t('admin', 'History'), 'url' => ['/cp/history/index'], 'icon' => 'list', 'visible' => Yii::$app->user->can('history')],
                    ['label' => Yii::t('admin', 'Sapphire points requests'), 'url' => ['/cp/balance/index', 'BalanceSearch' => ['status' => Balance::STATUS_WAITING]], 'icon' => 'align-center text-danger',
                     'visible' => Yii::$app->user->can('balance') && $hasPointsRequests],
                    ['label' => Yii::t('admin', 'Balances'), 'url' => ['/cp/balance/index'], 'icon' => 'coins', 'visible' => Yii::$app->user->can('balance')],
                    ['label' => Yii::t('admin', 'Payouts'), 'url' => ['/cp/payout/index'], 'icon' => 'hand-holding-usd', 'visible' => Yii::$app->user->can('payout')],
                    ['label' => Yii::t('admin', 'Sapphire tour'), 'url' => ['/cp/tour/index'], 'icon' => 'list-alt', 'visible' => Yii::$app->user->can('tour')],
                    ['label' => Yii::t('admin', 'Tours'), 'url' => ['/cp/tour-name/index'], 'icon' => 'list-alt', 'visible' => Yii::$app->user->can('tour')],
                    ['label' => Yii::t('admin', 'Pages'), 'url' => ['/cp/page/index'], 'icon' => 'file-alt', 'visible' => Yii::$app->user->can('page')],
                    ['label' => Yii::t('admin', 'News'), 'url' => ['/cp/news/index'], 'icon' => 'file-invoice', 'visible' => Yii::$app->user->can('news')],
                    ['label' => Yii::t('admin', 'Settings'), 'url' => ['/cp/setting/index'], 'icon' => 'cogs', 'visible' => Yii::$app->user->can('setting')],
                    ['label' => Yii::t('admin', 'Currency'), 'url' => ['/cp/currency/index'], 'icon' => 'coins', 'visible' => Yii::$app->user->can('currency')],
                    /*[
                        'label' => 'Starter Pages',
                        'icon'  => 'tachometer-alt',
                        'badge' => '<span class="right badge badge-info">2</span>',
                        'items' => [
                            ['label' => 'Active Page', 'url' => ['site/index'], 'iconStyle' => 'far'],
                            ['label' => 'Inactive Page', 'iconStyle' => 'far'],
                        ],
                    ],
                    ['label' => 'Simple Link', 'icon' => 'th', 'badge' => '<span class="right badge badge-danger">New</span>'],
                    ['label' => 'Yii2 PROVIDED', 'header' => true],
                    ['label' => 'Login', 'url' => ['site/login'], 'icon' => 'sign-in-alt', 'visible' => Yii::$app->user->isGuest],
                    ['label' => 'Gii', 'icon' => 'file-code', 'url' => ['/gii'], 'target' => '_blank'],
                    ['label' => 'Debug', 'icon' => 'bug', 'url' => ['/debug'], 'target' => '_blank'],
                    ['label' => 'MULTI LEVEL EXAMPLE', 'header' => true],
                    ['label' => 'Level1'],
                    [
                        'label' => 'Level1',
                        'items' => [
                            ['label' => 'Level2', 'iconStyle' => 'far'],
                            [
                                'label'     => 'Level2',
                                'iconStyle' => 'far',
                                'items'     => [
                                    ['label' => 'Level3', 'iconStyle' => 'far', 'icon' => 'dot-circle'],
                                    ['label' => 'Level3', 'iconStyle' => 'far', 'icon' => 'dot-circle'],
                                    ['label' => 'Level3', 'iconStyle' => 'far', 'icon' => 'dot-circle'],
                                ],
                            ],
                            ['label' => 'Level2', 'iconStyle' => 'far'],
                        ],
                    ],
                    ['label' => 'Level1'],
                    ['label' => 'LABELS', 'header' => true],
                    ['label' => 'Important', 'iconStyle' => 'far', 'iconClassAdded' => 'text-danger'],
                    ['label' => 'Warning', 'iconClass' => 'nav-icon far fa-circle text-warning'],
                    ['label' => 'Informational', 'iconStyle' => 'far', 'iconClassAdded' => 'text-info'],
                    */
                    ['label' => Yii::t('account', 'System'), 'header' => true],
                    ['label' => Yii::t('site', 'Logout'), 'url' => ['/site/logout'], 'icon' => 'sign-in-alt', 'visible' => !Yii::$app->user->isGuest],
                ],
            ]);
            ?>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>