<?php

namespace app\components\columns;

use rmrevin\yii\fontawesome\FAS;
use Yii;
use yii\base\InvalidConfigException;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii2tech\admin\grid\PositionColumn as AdminPositionColumn;

class PositionColumn extends AdminPositionColumn
{

    public $enableAjax = true;

    public function init() {
        parent::init();
        $this->contentOptions['class'] = 'text-nowrap';
        $this->headerOptions['style'] = 'width:50px';
        if ($this->enableAjax) {
            $this->registerJs();
        }
    }

    protected function renderButton($name, $model, $key, $index) {
        if (!isset($this->buttons[$name])) {
            return '';
        }
        $button = $this->buttons[$name];

        if ($button instanceof \Closure) {
            $url = $this->createUrl($name, $model, $key, $index);
            return call_user_func($button, $url, $model, $key);
        }
        if (!is_array($button)) {
            throw new InvalidConfigException("Button should be either a Closure or array configuration.");
        }

        // Visibility :
        if (isset($button['visible'])) {
            if ($button['visible'] instanceof \Closure) {
                if (!call_user_func($button['visible'], $model, $key, $index)) {
                    return '';
                }
            } else if (!$button['visible']) {
                return '';
            }
        }

        // URL :
        if (isset($button['url'])) {
            $url = call_user_func($button['url'], $name, $model, $key, $index);
        } else {
            $url = $this->createUrl($name, $model, $key, $index);
        }

        // label :
        if (isset($button['label'])) {
            $label = $button['label'];

            if (isset($button['encode'])) {
                $encodeLabel = $button['encode'];
                unset($button['encode']);
            } else {
                $encodeLabel = true;
            }
            if ($encodeLabel) {
                $label = Html::encode($label);
            }
        } else {
            $label = '';
        }

        // icon :
        if (isset($button['icon'])) {
            $icon = $button['icon'];
            $label = FAS::icon($icon) . (empty($label) ? '' : ' ' . $label);
        }

        $options = array_merge(ArrayHelper::getValue($button, 'options', []), $this->buttonOptions, ['class' => 'position-column']);

        return Html::a($label, $url, $options);
    }

    protected function initDefaultButtons() {
        $sort = Yii::$app->request->get($this->grid->dataProvider->sort ? $this->grid->dataProvider->sort->sortParam : 'sort');
        parent::initDefaultButtons();
        $this->buttons = ArrayHelper::merge($this->buttons, [
            'first' => [
                'icon'    => $sort == '-position' ? 'angle-double-down' : 'angle-double-up',
                'visible' => function ($model, $key, $index) use ($sort) {
                    return $sort == '-position' ? $index + 1 < $this->grid->dataProvider->getTotalCount() : $index > 0;
                },
            ],
            'last'  => [
                'icon'    => $sort == '-position' ? 'angle-double-up' : 'angle-double-down',
                'visible' => function ($model, $key, $index) use ($sort) {
                    return $sort == '-position' ? $index > 0 : $index + 1 < $this->grid->dataProvider->getTotalCount();
                },
            ],
            'prev'  => [
                'icon'    => $sort == '-position' ? 'arrow-down' : 'arrow-up',
                'visible' => function ($model, $key, $index) use ($sort) {
                    return $sort == '-position' ? $index + 1 < $this->grid->dataProvider->getTotalCount() : $index > 0;
                },
            ],
            'next'  => [
                'icon'    => $sort == '-position' ? 'arrow-up' : 'arrow-down',
                'visible' => function ($model, $key, $index) use ($sort) {
                    return $sort == '-position' ? $index > 0 : $index + 1 < $this->grid->dataProvider->getTotalCount();
                },
            ],
        ]);
    }

    public function getDataCellValue($model, $key, $index) {
        if (isset($model->behaviors()['nestedSetsBehavior']) && $model->{$model->depthAttribute} > 1) {
            return $index + 1;
        }
        return parent::getDataCellValue($model, $key, $index);
    }

    public function registerJs() {
        $js = <<< JS
            $('body').on('click', 'a.position-column', function(e) {
                e.preventDefault();
                if($(this).hasClass('clicked')) {
                    return false;
                }
                $(this).addClass('clicked');
                $.post($(this).attr('href'), function(data) {
                  $(e.target).removeClass('clicked');
                  var pjaxId = $(e.target).closest('.grid-view').parent().attr('id');
                  $.pjax.reload({container:'#' + pjaxId, timeout: 5000});
                });
                return false;
            });
JS;
        $this->grid->view->registerJs($js, View::POS_READY, 'yii2-position-column');
    }

}