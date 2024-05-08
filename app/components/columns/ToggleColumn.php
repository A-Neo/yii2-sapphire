<?php

namespace app\components\columns;

use rmrevin\yii\fontawesome\FAS;
use Yii;
use yii\bootstrap4\Html;
use yii\helpers\Inflector;
use yii\web\View;

class ToggleColumn extends \yii2mod\toggle\ToggleColumn
{

    public $extended = null;
    public $readonly = false;
    public $icons    = null;
    public $asLabel  = false;

    public function init() {
        $this->headerOptions['style'] = 'width:50px';
        if($filterModel = $this->grid->filterModel){
            if($filterModel->hasMethod($method = 'get' . ucfirst(Inflector::pluralize($this->attribute)) . 'List')){
                $this->filter = $filterModel->$method();
            }
        }
        parent::init();
    }

    protected function renderDataCellContent($model, $key, $index) {
        $attribute = $this->attribute;
        $value = $model->$attribute;
        if($this->asLabel){
            $method = 'get' . ucfirst($this->attribute) . 'Name';
            $icon = $model->$method();
        }else{
            if(!empty($this->icons) && isset($this->icons[$value])){
                $icon = FAS::icon($this->icons[$value]);
            }else{
                $icon = FAS::icon($value < 0 ? 'lock text-danger' : ($value > 1 ? 'check-double text-success' : ($value ? 'check text-success' : 'times text-danger')));
            }
        }
        $readonly = $this->readonly;
        if($readonly instanceof \Closure){
            $readonly = call_user_func($readonly, $model);
        }
        if($readonly){
            return $icon;
        }
        $url = [$this->action, 'id' => $model->id, 'extended' => $this->extended, 'attribute' => $attribute, 'table' => Yii::$app->request->get('table')];
        return Html::a(
            $icon,
            $url,
            [
                //'title'       => ($value === null || $value == true) ? Yii::t('site', 'Off') : Yii::t('site', 'On'),
                'class'       => 'toggle-column',
                'data-method' => 'post',
                'data-pjax'   => '0',
            ]
        );
    }

    public function registerJs() {
        $js = <<< JS
            $('body').on('click', 'a.toggle-column', function(e) {
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
        $this->grid->view->registerJs($js, View::POS_READY, 'yii2mod-toggle-column');
    }
}