<?php


namespace app\components\columns;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQueryInterface;
use yii\db\Connection;
use yii\di\Instance;
use yii\grid\Column;
use yii\grid\DataColumn;
use yii\helpers\Html;
use yii\helpers\Inflector;

class ComposedColumn extends DataColumn
{

    public $values = [];
    public $separator = '<br/>';

    protected function renderDataCellContent($model, $key, $index) {
        $result = [];
        foreach ($this->values as $value) {
            $result[] = $value->renderDataCellContent($model, $key, $index);
        }
        return implode($this->separator, $result);
    }

    public function init() {
        parent::init();
        foreach ($this->values as $k => $value) {
            if (is_array($value)) {
                $value['grid'] = $this->grid;
            }
            $this->values[$k] = $value = Instance::ensure($value, Column::class);
            if (empty($this->filter) && !empty($value->filter)) {
                $this->filter = $value->filter;
            }
        }
    }

}