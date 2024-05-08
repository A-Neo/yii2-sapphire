<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;

if(Yii::$app->controller->module->id == 'account' || strpos(trim(Yii::$app->request->url, '/'), 'pm') === 0){
    \app\assets\AccountAsset::register($this);
}
if(Yii::$app->controller->module->id == 'admin' || strpos(trim(Yii::$app->request->url, '/'), 'cp') === 0){
    \app\assets\AdminAsset::register($this);
}
$this->registerCssFile('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback');

$assetDir = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language?>">
<head>
    <meta charset="<?=Yii::$app->charset?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?=Html::encode($this->title)?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition sidebar-mini">
<?php $this->beginBody() ?>

<div class="wrapper">
    <!-- Navbar -->
    <?=$this->render('navbar', ['assetDir' => $assetDir])?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?=$this->render('sidebar', ['assetDir' => $assetDir])?>

    <!-- Content Wrapper. Contains page content -->
    <?=$this->render('content', ['content' => $content, 'assetDir' => $assetDir])?>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <?=$this->render('control-sidebar')?>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    <?=$this->render('footer')?>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
