<?php

namespace app\modules\account;

use Yii;

class Module extends \yii\base\Module
{

    public $id = 'account';

    public $controllerNamespace = 'app\modules\account\controllers';

    public function init() {
        Yii::$app->layoutPath = \Yii::$app->basePath . '/views/account/layouts';
        $this->viewPath = \Yii::$app->basePath . '/views/account';
        Yii::$app->cache->flush();
        parent::init();
    }

}
