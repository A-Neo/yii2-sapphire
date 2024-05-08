<?php

use yii\web\HttpException;

// define('YII_DEBUG', !empty($_SERVER['USER']) && ($_SERVER['USER'] == 'fredy'));
define('YII_DEBUG', true);
define('TEST_MODE', YII_DEBUG);
define('ROOT_DIR', __DIR__);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/config/common.php',
    require __DIR__ . '/config/web.php'
);

$application = new yii\web\Application($config);
$exitCode = $application->run();

return $exitCode;