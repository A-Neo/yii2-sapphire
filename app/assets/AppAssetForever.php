<?php

namespace app\assets;

use yii\web\View;

class AppAssetForever extends Asset
{
    public $sourcePath = '@root/views/assets/forever';

    public $css = [
        'lib/fontawesome-free/css/all.min.css',
        'css/style.css',
//        ['images/favicon.ico', 'rel' => 'shortcut icon', 'type' => 'image/x-icon'],
//        ['images/webclip.png', 'rel' => 'apple-touch-icon'],
    ];

    public $js = [
        'js/script.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset', // - подключается как зависимость от YiiAsset
         'yii\jui\JuiAsset',
         //'app\assets\LightboxAsset',
         'yii\bootstrap4\BootstrapAsset',
    ];
}
