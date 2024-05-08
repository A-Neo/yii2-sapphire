<?php

namespace app\assets;

use yii\web\View;

class AppAsset extends Asset
{
    public $sourcePath = '@root/views/assets';

    public $css = [
        'common.css',
        'style.css',
        'main.css',
        ['images/favicon.ico', 'rel' => 'shortcut icon', 'type' => 'image/x-icon'],
        ['images/webclip.png', 'rel' => 'apple-touch-icon'],
    ];

    public $js = [
        'common.js',
        'script.js',
        ['https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js', 'position' => View::POS_HEAD],
        [1, 'content' => 'WebFont.load({  google: {    families: ["Montserrat:100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic"]  }});', 'position' => View::POS_HEAD],
        ['https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js', 'position' => View::POS_HEAD],
        [2, 'content' => '!function(o,c){var n=c.documentElement,t=" w-mod-";n.className+=t+"js",("ontouchstart"in o||o.DocumentTouch&&c instanceof DocumentTouch)&&(n.className+=t+"touch")}(window,document);', 'position' => View::POS_HEAD],
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset', // - подключается как зависимость от YiiAsset
         'yii\jui\JuiAsset',
         //'app\assets\LightboxAsset',
         'yii\bootstrap4\BootstrapAsset',
         'yii\bootstrap4\BootstrapPluginAsset',
         'rmrevin\yii\fontawesome\NpmFreeAssetBundle',
    ];
}
