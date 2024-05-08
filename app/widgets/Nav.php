<?php

namespace app\widgets;

use yii\base\InvalidConfigException;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

class Nav extends \yii\bootstrap4\Nav
{

    public function renderItems() {
        $tag = 'ul';
        if(is_array($this->options) && !empty($this->options['tag'])){
            $tag = $this->options['tag'];
            unset($this->options['tag']);
        }
        $items = [];
        foreach($this->items as $i => $item){
            if(isset($item['visible']) && !$item['visible']){
                continue;
            }
            $items[] = $this->renderItem($item);
        }

        return Html::tag($tag, implode("\n", $items), $this->options);
    }

    public function renderItem($item) {
        if(is_string($item)){
            return $item;
        }
        if(!isset($item['label'])){
            throw new InvalidConfigException("The 'label' option is required.");
        }
        $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
        $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
        $options = ArrayHelper::getValue($item, 'options', []);
        $items = ArrayHelper::getValue($item, 'items');
        $url = ArrayHelper::getValue($item, 'url', '#');
        $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);
        $disabled = ArrayHelper::getValue($item, 'disabled', false);
        $active = $this->isItemActive($item);

        if(empty($items)){
            $items = '';
            Html::addCssClass($options, ['widget' => 'nav-item']);
            Html::addCssClass($linkOptions, ['widget' => 'nav-link']);
        }else{
            $linkOptions['data-toggle'] = 'dropdown';
            Html::addCssClass($options, ['widget' => 'dropdown nav-item']);
            Html::addCssClass($linkOptions, ['widget' => 'dropdown-toggle nav-link']);
            if(is_array($items)){
                $items = $this->isChildActive($items, $active);
                $items = $this->renderDropdown($items, $item);
            }
        }

        if($disabled){
            ArrayHelper::setValue($linkOptions, 'tabindex', '-1');
            ArrayHelper::setValue($linkOptions, 'aria-disabled', 'true');
            Html::addCssClass($linkOptions, ['disable' => 'disabled']);
        }else if($this->activateItems && $active){
            Html::addCssClass($linkOptions, ['activate' => 'active']);
        }
        $tag = 'li';
        if(isset($options['tag'])){
            $tag = $options['tag'];
            unset($options['tag']);
        }
        if(empty($item['active']) && !empty($linkOptions['class']['activate'])){
            unset($linkOptions['class']['activate']);
        }
        if(empty($tag)){
            return Html::a($label, $url, $linkOptions) . $items;
        }
        return Html::tag($tag, Html::a($label, $url, $linkOptions) . $items, $options);
    }

}
