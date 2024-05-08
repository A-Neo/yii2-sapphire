<?php

namespace app\widgets;

use yii\helpers\ArrayHelper;

class Tabs extends \yii\bootstrap4\Tabs
{

    public $linkOptions = [];

    protected function prepareItems(&$items, $prefix = '') {
        parent::prepareItems($items, $prefix);
        foreach($items as $n => $item){
            $linkOptions = ArrayHelper::getValue($item, 'options', []);
            ArrayHelper::setValue($items[$n], 'linkOptions', ArrayHelper::merge($this->linkOptions, $linkOptions));
        }
    }

}