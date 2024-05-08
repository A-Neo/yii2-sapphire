<?php

namespace app\components\columns;

use Yii;
use yii\bootstrap4\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;

class DataColumn extends \yii\grid\DataColumn
{

    public function init() {
        if($filterModel = $this->grid->filterModel){
            if($filterModel->hasMethod($method = 'get' . ucfirst(Inflector::pluralize($this->attribute)) . 'List')){
                $this->filter = $filterModel->$method();
            }
        }
        parent::init();
    }

    protected function renderDataCellContent($model, $key, $index) {
        if(!$this->value instanceof \Closure && $model->hasMethod($method = 'get' . ucfirst($this->attribute) . 'Name')){
            return $model->$method();
        }
        return parent::renderDataCellContent($model, $key, $index);
    }

}