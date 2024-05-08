<?php

namespace app\components;

use yii\helpers\ArrayHelper;
use yii\bootstrap4\Html;

class View extends \yii\web\View
{
    public function registerJsFile($url, $options = [], $key = null) {
        if(empty($options['type'])){
            $options['type'] = 'text/javascript';
        }
        if(!empty($options['content'])){
            $position = ArrayHelper::remove($options, 'position', self::POS_END);
            $key = md5($options['content']);
            $content = $options['content'];
            unset($options['content']);
            $this->jsFiles[$position][$key] = Html::tag('script', $content, $options);
            return;
        }
        parent::registerJsFile($url, $options, $key);
    }

}
