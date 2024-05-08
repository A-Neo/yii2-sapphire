<?php

namespace app\assets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\AssetBundle;

class Asset extends AssetBundle
{
    public static function image($image, $appendTimeStamp = true) {
        $manager = Yii::$app->view->getAssetManager();
        $bundle = $manager->getBundle(get_called_class());
        return $manager->getAssetUrl($bundle, 'images/' . $image, $appendTimeStamp);
    }

    public static function favicon($image, $appendTimeStamp = true) {
        $manager = Yii::$app->view->getAssetManager();
        $bundle = $manager->getBundle(get_called_class());
        return $manager->getAssetUrl($bundle, 'favicon/' . $image, $appendTimeStamp);
    }
}
