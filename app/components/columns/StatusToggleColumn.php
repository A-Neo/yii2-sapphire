<?php

namespace app\components\columns;

use Yii;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class StatusToggleColumn extends \yii\grid\DataColumn
{

    public $model;
    public $permission = 'moderator';
    public $data       = null;
    public $modelName;
    public $captionOptions;
    public $invert     = false;
    public $link       = true;
    public $modalYes   = false;
    public $modalNo    = false;
    public $modal      = 'moderator-permissions';

    public function renderDataCellContent($model, $key, $index) {
        $link = $this->link;
        if ($this->link === 'yes') {
            $link = $model->{$this->attribute};
        } else if ($this->link === 'no') {
            $link = !$model->{$this->attribute};
        }
        $addLink = false;
        if ((Yii::$app->user->can('admin') || Yii::$app->user->can($this->permission)) && $link) {
            $attributes = [];
            $aa = [];
            if (($this->modalYes && $model->{$this->attribute}) || ($this->modalNo && !$model->{$this->attribute})) {
                $attributes = [
                    'data-toggle'     => 'modal',
                    'data-target'     => '#' . $this->modal,
                    'data-do'         => 'toggle',
                    'data-attributes' => function ($model) {
                        return $this->data ? ArrayHelper::getValue($model, $this->data) : $model->getAttributes();
                    },
                    'data-id'         => function ($model) {
                        return $model->id;
                    },
                    'data-backdrop'   => 'false',
                    'class'           => 'btn-with-confirm',
                ];
            }
            foreach ($attributes as $key => $attribute) {
                if ($attribute instanceof \Closure) {
                    $attributes[$key] = call_user_func($attribute, $model);
                }
            }
            if ($this->modalYes && $this->modalYes !== true && $this->modalYes !== '1' && $model->{$this->attribute}) {
                $aa = $attributes;
                $attributes = [];
                $addLink = $this->modalYes;
            }
            if ($this->modalNo && $this->modalNo !== true && $this->modalNo !== '1' && !$model->{$this->attribute}) {
                $aa = $attributes;
                $attributes = [];
                $addLink = $this->modalYes;
            }
            $attributes['href'] = $attributes['href'] ?? Url::to(['/cp/default/toggle', 'id' => $model->id, 'model' => $this->modelName, 'field' => $this->attribute, 'value' => $model->{$this->attribute} ? 0 : 1, 'modalYes' => $this->modalYes, 'modalNo' => $this->modalNo, 'data' => $this->data, 'link' => $this->link, 'no-cache' => time()]);
            $attributes['class'] = ($attributes['class'] ?? 'ajax') . ' ' . (($this->invert ? !$model->{$this->attribute} : $model->{$this->attribute}) ? 'text-success' : 'text-danger');
            $attributes = Html::renderTagAttributes($attributes);
            $result = '<a ' . $attributes . '>' . ($model->{$this->attribute} ? Yii::t('site', 'Yes') : Yii::t('site', 'No')) . '</a>';
            if ($addLink &&
                (($model->{$this->attribute} && $this->modalYes && $this->modalYes !== true)
                    || (!$model->{$this->attribute} && $this->modalNo && $this->modalNo !== true))
            ) {
                $aa['href'] = $aa['href'] ?? Url::to(['/cp/default/toggle', 'id' => $model->id, 'model' => $this->modelName, 'field' => $this->attribute, 'value' => $model->{$this->attribute} ? 1 : 0, 'modalYes' => $this->modalYes, 'modalNo' => $this->modalNo, 'data' => $this->data, 'link' => $this->link, 'no-cache' => time()]);
                $aa['class'] = ($aa['class'] ?? 'ajax') . ' text-success';
                $attributes = Html::renderTagAttributes($aa);
                $result .= ' (<a ' . $attributes . '>' . $addLink . '</a>)';
            }
            return $result;
        } else {
            return '<span class="' . (($this->invert ? !$model->{$this->attribute} : $model->{$this->attribute}) ? 'text-success' : 'text-danger') . '" >' . ($model->{$this->attribute} ? Yii::t('site', 'Yes') : Yii::t('site', 'No')) . '</span>';
        }
    }

    public function init() {
        parent::init();
        $this->filter = [
            '0' => Yii::t('site', 'No'),
            '1' => Yii::t('site', 'Yes'),
        ];
    }

}
