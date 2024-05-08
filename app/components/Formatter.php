<?php

namespace app\components;

use Yii;
use yii\bootstrap4\Html;

class Formatter extends \yii\i18n\Formatter
{

    public $datetimeFormat = 'php:d-m-Y H:i:s';

    public $dateFormat = 'php:d-m-Y';

    public $timeFormat = 'php:H:i:s';

    public $currencyDecimalSeparator = '.';

    public $currencyCode = 'USD';

    public $booleanFormat = ['<span class="fas fa-times text-danger"></span>', '<span class="fas fa-check text-success"></span>'];

    public function asFormated($value, $format) {
        return sprintf($format, $value);
    }

    public function asMasked($value) {
        if(strpos($value, '<br/>')){
            $values = explode('<br/>', $value);
            foreach($values as $i => $value){
                $values[$i] = $this->asMasked($value);
            }
            return implode('<br/>', $values);
        }
        return substr($value, 0, 4) . str_repeat('*', strlen(substr($value, 4, -4))) . substr($value, -4);
    }

    public function asDays($value) {
        if($value === null){
            return $this->nullDisplay;
        }
        return Yii::t('site', '{count, plural, one{# day} other{# days}}', ['count' => $value]);
    }

    public function asRate($value) {
        return number_format($value, 2) . ' %';
    }


    public function asBl($value) {
        $bl = Yii::$app->user->identity->bl;
        return Html::tag('span', $value . ($bl >= $value ? ' <= ' : ' > ') . $bl, ['class' => 'text-' . ($bl >= $value ? 'success' : 'danger')]);
    }

    public function asTl($value) {
        $tl = Yii::$app->user->identity->tl;
        return Html::tag('span', $value . ($tl >= $value ? ' <= ' : ' > ') . $tl, ['class' => 'text-' . ($tl >= $value ? 'success' : 'danger')]);
    }

    public function asThumbs($value, $options = []) {
        if(empty($value)){
            return $this->nullDisplay;
        }
        if(!is_array($value)){
            $value = [$value];
        }
        $result = '';
        $addstyle = '';
        if(!empty($options['max']) && count($value) > 1){
            $addstyle = 'style="width:' . round(100 / min(count($value), $options['max'])) . 'px;display:inline-block;"';
        }else if(!empty($options['inline'])){
            $addstyle = 'style="display:inline-block;"';
        }
        foreach($value as $i => $item){
            if(empty($item)){
                continue;
            }
            if(!empty($options['max']) && $i >= $options['max']){
                continue;
            }
            $result .= '<div class="col-xs-1 m-1 " ' . $addstyle . '>';
            if(!empty($options['delete'])){
                $result .= '<div class="custom-control custom-checkbox">';
                $result .= '<input type="hidden" name="' . $options['delete'] . '[delete][' . $i . ']" value="">';
                $result .= '<input type="checkbox" id="check-' . $i . '" class="custom-control-input" name="' . $options['delete'] . '[delete][' . $i . ']" value="' . $item . '">';
                $result .= '<label class="custom-control-label" for="check-' . $i . '">' . Yii::t('site', 'Delete') . '</label>';
                $result .= '</div>';
                //   $form->field($model, 'delete[]')->checkbox(['value' => $passport, 'id' => 'check-' . $n])->label(Yii::t('site', 'Delete')
                //   $result .= '<a href="' . $this->url($item) . '" class="pull-left" data-lightbox="thumbs">';
            }
            if(!empty($options['position'])){
                $result .= '<div class="form-group field-product-image-position">';
                $result .= '<input type="number" style="width:100px;" class="form-control" title="' . Yii::t('site', 'Position') . '" name="' . $options['position'] . '[position][' . $i . ']" value="' . ($i + 1) . '" aria-invalid="false">';
                $result .= '</div>';
            }
            $result .= '<a href="' . $this->url($item) . '" class="pull-left" data-lightbox="thumbs">';
            $result .= '<img class="thumbnail" src="' . $this->thumb($item) . '" ' . $addstyle . '></a>';
            $result .= '</div>';
        }
        return $result;
    }

    public function asArray($value) {
        if(empty($value)){
            return $value;
        }
        if(is_string($value)){
            try{
                $value = json_decode($old = $value, true);
            }catch(\Exception $e){
                $value = $old;
            }
        }
        if(is_array($value)){
            $result = '';
            foreach($value as $key => $val){
                $result .= $key . ': ' . $val . "<br/>\n";
            }
            return $result;
        }
        return $value;
    }

    public function asTString($value) {
        if(is_array($value)){
            $languages = Yii::$app->languagesDispatcher->languages;
            if(count($value) == 1 && count($languages) == 1){
                return current($value);
            }
            $result = [];
            foreach($value as $k => $v){
                $result[] = $k . ': ' . $v;
            }
            return implode('<br/>', $result);
        }
        return $value;
    }

    public function asUrl($value, $options = []) {
        if($value === null){
            return $this->nullDisplay;
        }
        $url = $value;
        if(strpos($url, '://') === false && strpos($url, '/') !== 0){
            $url = 'http://' . $url;
        }

        return Html::a(Html::encode($value), $url, $options);
    }
}
