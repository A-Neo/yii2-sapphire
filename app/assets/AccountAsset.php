<?php

namespace app\assets;

class AccountAsset extends Asset
{
    public $sourcePath = '@root/views/account/assets';

    public $css = [
        'common.css',
    ];

    public $js = [
        'common.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset', // - подключается как зависимость от YiiAsset
        'yii\jui\JuiAsset',
        //'app\assets\LightboxAsset',
        'yii\bootstrap4\BootstrapAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
        'rmrevin\yii\fontawesome\NpmFreeAssetBundle',
        'hail812\adminlte3\assets\AdminLteAsset',
    ];
}
