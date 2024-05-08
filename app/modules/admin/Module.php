<?php

namespace app\modules\admin;

use Yii;

class Module extends \yii\base\Module
{

    public $id = 'admin';

    public $controllerNamespace = 'app\modules\admin\controllers';

    public function init() {
        Yii::$app->language='ru';
        Yii::$app->layoutPath = \Yii::$app->basePath . '/views/admin/layouts';
        $this->viewPath = \Yii::$app->basePath . '/views/admin';
        Yii::$app->cache->flush();
        parent::init();
    }

}
