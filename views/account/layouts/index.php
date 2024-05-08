<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\AppAssetForever;
use app\widgets\Alert;
use app\models\Balance;

$user = Yii::$app->user->identity;
AppAssetForever::register($this);
$hasPoints = Balance::find()->
where(['to_user_id' => $user->id, 'status' => Balance::STATUS_WAITING, 'comment' => ''])
    ->orWhere(['from_user_id' => $user->id])
    ->exists();
\yii\helpers\VarDumper::dump($hasPoints, 5, true);

?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language?>">
<head>
    <meta charset="<?=Yii::$app->charset?>">
    <title><?=Html::encode($this->title)?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rampart+One&family=Roboto&display=swap" rel="stylesheet">
    <?php $this->registerCsrfMetaTags() ?>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="main-wrapper">
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar" >

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav  nav-sidebar flex-column" role="menu" data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
                         with font-awesome or any other icon font library -->
                    <?=$this->render('menu-forever');?>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
    <header class="main-header">
    <nav class="main-nav">
        <ul class="nav-list">
            <li class="nav-item nav-item-hamburger">
                <a class="nav-link nav-link-hamburger " data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item">
                <a href="/pm/balance" class="nav-link nav-link-btn">
                    <?=Yii::t('account', 'Balance')?>:
                    <?=number_format($user->balance - $user->accumulation, 2)?> $</a>
            </li>
            <li class="nav-item">
                <?php
                if($hasPoints): ?>
                <a href="/pm/profile" class="nav-link nav-link-btn"><?=Yii::t('account', 'Profile')?> <i class="fas fa-user-tie"></i></a>
                <?php endif ?>
            </li>
        </ul>
    </nav>
</header>
</div>
<main class="main">
    <aside class="main-sidebar main-sidebar-desktop" >

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav  nav-sidebar flex-column" role="menu" data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
                         with font-awesome or any other icon font library -->

                    <?=$this->render('menu-forever');?>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
    <div class="body-wrapper">
        <?=$content?>
    </div>
</main>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>