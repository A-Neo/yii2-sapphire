<?php

namespace app\components;

use cetver\LanguagesDispatcher\handlers\AbstractHandler;
use Yii;

class AdminLanguageHandler extends AbstractHandler
{
    public function getLanguages() {
        if(Yii::$app->controller->module->id == 'cp'){
            return ['ru'];
        }
        return [];
    }
}