<?php

namespace app\components\columns;

use yii\base\InvalidConfigException;
use yii\grid\DataColumn;
use yii\helpers\Inflector;
use yii\web\JsExpression;

class EditableColumn extends \yii2mod\editable\EditableColumn
{

    public $url             = ['edit'];
    public $editableOptions = [
        'mode' => 'inline',
    ];

    public function init() {
        //parent::init();
        if ($this->filterAttribute === null) {
            $this->filterAttribute = $this->attribute;
        }
        if ($this->url === null) {
            throw new InvalidConfigException('The "url" property must be set.');
        }
        $this->clientOptions['success'] = new JsExpression('function (scope, response, newValue) {$.pjax.reload({container: \'[data-pjax-container]\', async: false});return {newValue:response}}');
        $rel = $this->attribute . '_editable' . $this->classSuffix;
        $this->options['pjax'] = 1;
        $this->options['rel'] = $rel;
        if ($filterModel = $this->grid->filterModel) {
            if ($filterModel->hasMethod($method = 'get' . ucfirst(Inflector::pluralize($this->attribute)) . 'List')) {
                $this->filter = $filterModel->$method();
            }
        }
        $this->registerClientScript();
    }

    protected function renderDataCellContent($model, $key, $index) {
        $opts = $this->editableOptions;
        if (is_callable($this->editableOptions)) {
            $opts = call_user_func($this->editableOptions, $model, $key, $index);
        }
        if (!empty($opts['disabled'])) {
            return DataColumn::renderDataCellContent($model, $key, $index);
        }
        $this->options['data-value'] = $model->{$this->attribute};
        return parent::renderDataCellContent($model, $key, $index); // TODO: Change the autogenerated stub
    }

}