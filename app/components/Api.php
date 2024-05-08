<?php

namespace app\components;

include_once __DIR__ . '/cpayeer.php';

use app\models\History;
use CPayeer;
use Yii;
use rmrevin\yii\fontawesome\FAS;

class Api
{

    public static function asNumber($n, $sign = true) {
        return number_format($n, 2, '.', '`') . ($sign ? ' ' . FAS::icon('dollar-sign') : '');
    }

    public static function balance($formated = true) {
        if(empty(Yii::$app->params['api']['account']) || empty(Yii::$app->params['api']['id']) || empty(Yii::$app->params['api']['key'])){
            if(!$formated){
                return 0;
            }
            return 'Empty api params';
        }
        $payeer = new CPayeer(Yii::$app->params['api']['account'], Yii::$app->params['api']['id'], Yii::$app->params['api']['key']);
        if($payeer->isAuth()){
            $balance = $payeer->getBalance();
            $n = !empty($balance['balance']['USD']['BUDGET']) ? floatval($balance['balance']['USD']['BUDGET']) : 0;
            return $formated ? self::asNumber($n) : $n;
        }else{
            if(!$formated){
                return 0;
            }
            return json_encode($payeer->getErrors());
        }
    }

    public static function updateHistory($echo = false) {
        file_put_contents(__DIR__ .'/history.txt', 'updateHistory');
        $history = self::history(History::find()->where(['currency' => History::CURRENCY_PAYEER])->orderBy(['id' => SORT_DESC])->select('id')->scalar());
        if(!empty($history)){
            file_put_contents(__DIR__ .'/history.txt', print_r($history, true));
            $ids = array_column($history, 'id');
            $exists = History::find()->where(['id' => $ids])->select('id')->column();
            if($echo){
                echo 'Found: ' . count($history) . ' items.' . ($exists ? ' ' . count($exists) . ' exists.' : '') . "\n";
            }
            foreach($history as $item){
                if(in_array($item['id'], $exists)){
                    continue;
                }
                $model = new History();
                $item['currency'] = History::CURRENCY_PAYEER;
                $model->setAttributes($item);
                if(!$model->save() && $echo){
                    var_dump($model->getErrors());
                    exit;
                }
            }
        } else {
            file_put_contents(__DIR__ .'/history.txt', 'updateHistory empty');
        }
    }

    public static function history($lastId = null) {
        if(empty(Yii::$app->params['api']['account']) || empty(Yii::$app->params['api']['id']) || empty(Yii::$app->params['api']['key'])){
            return [];
        }
        $payeer = new CPayeer(Yii::$app->params['api']['account'], Yii::$app->params['api']['id'], Yii::$app->params['api']['key']);
        if($payeer->isAuth()){
            $history = $payeer->getHistoryTransactions(date('Y-m-d H:i:s', strtotime('-2 days')), date('Y-m-d H:i:s', strtotime('+5 hour')), 1000, $lastId);
            if(!empty($history['history'])){
                return $history['history'];
            }
            return [];
        }else{
            return [];
        }
    }

}