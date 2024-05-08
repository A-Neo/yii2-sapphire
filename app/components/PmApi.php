<?php

namespace app\components;

include_once __DIR__ . '/cpayeer.php';

use app\models\History;
use CPayeer;
use Yii;
use rmrevin\yii\fontawesome\FAS;
use yii\base\BaseObject;

class PmApi
{
    public static function balance($formated = true) {
        if(empty(Yii::$app->params['api']['pm_account']) || empty(Yii::$app->params['api']['pm_login']) || empty(Yii::$app->params['api']['pm_pass'])){
            if(!$formated){
                return 0;
            }
            return 'Empty api params';
        }
        $reply = self::call_post('https://perfectmoney.is/acct/balance.asp', [
            'AccountID'  => Yii::$app->params['api']['pm_login'],
            'PassPhrase' => Yii::$app->params['api']['pm_pass'],
        ]);
        if(preg_match('|Error:[^\n]*|ism', $reply, $m)){
            return $m[0];
        }
        if(preg_match('#name=(\'|")(' . Yii::$app->params['api']['pm_account'] . ')(\'|").*?value=(\'|")(.*?)(\'|")#is', $reply, $m)){
            return $formated ? Api::asNumber($m[5]) : floatval($m[5]);
        }
        return 0;
    }

    public static function updateHistory($echo = false) {
        $history = self::history(History::find()->where(['currency' => History::CURRENCY_PERFECT])->orderBy(['date' => SORT_DESC])->select('date')->scalar());
        if(!empty($history)){
            $ids = array_column($history, 'id');
            $exists = History::find()->where(['id' => $ids, 'currency' => 'Perfect Money'])->select('id')->column();
            if($echo){
                echo 'Found: ' . count($history) . ' items.' . ($exists ? ' ' . count($exists) . ' exists.' : '') . "\n";
            }
            foreach($history as $item){
                if(in_array($item['id'], $exists)){
                    continue;
                }
                $model = new History();
                $item['currency'] = History::CURRENCY_PERFECT;
                $model->setAttributes($item);
                if(!$model->save() && $echo){
                    var_dump($model->getErrors());
                    exit;
                }
            }
        }
    }

    public static function history($lastDate = null) {
        if(empty(Yii::$app->params['api']['pm_account']) || empty(Yii::$app->params['api']['pm_login']) || empty(Yii::$app->params['api']['pm_pass'])){
            return [];
        }
        if($lastDate){
            $p = date('Y-m-d', strtotime(' -28 day'));
            if($lastDate < $p) {
                $prev_date = $p;
            } else {
                $prev_date = explode(' ', $lastDate)[0];
            }
        }else{
            $prev_date = date('Y-m-d', strtotime(' -28 day'));
        }
        $prev_date = explode("-", $prev_date);
        $next_date = date('Y-m-d', strtotime(' +1 day'));
        $next_date = explode("-", $next_date);
        $params = [
            'AccountID'  => Yii::$app->params['api']['pm_login'],
            'PassPhrase' => Yii::$app->params['api']['pm_pass'],
            'startmonth' => $prev_date[1],
            'startday'   => $prev_date[2],
            'startyear'  => $prev_date[0],
            'endmonth'   => $next_date[1],
            'endday'     => $next_date[2],
            'endyear'    => $next_date[0],
        ];
        $reply = self::call_post('https://perfectmoney.is/acct/historycsv.asp', $params);
        $result = [];
        $header = false;
        foreach(explode("\n", $reply) as $line){
            if(empty($line)){
                continue;
            }
            $line = str_getcsv($line);
            if(!$header){
                $header = $line;
                continue;
            }
            if(count($header) == count($line)){
                $row = array_combine($header, $line);
                $row['Memo'] = $row['Memo'] . (!empty($row['Payment ID']) ? ' ID:' . $row['Payment ID'] : '');
                $result[] = [
                    'id'               => $row['Batch'],
                    'date'             => date('Y-m-d H:i:s', strtotime($row['Time'])),
                    'from'             => $row['Payer Account'],
                    'to'               => $row['Payee Account'],
                    'creditedAmount'   => $row['Amount'],
                    'creditedCurrency' => $row['Currency'],
                    'debitedAmount'    => $row['Amount'] + $row['Fee'],
                    'debitedCurrency'  => $row['Currency'],
                    'status'           => $row['Type'],
                    'comment'          => $row['Memo'],
                    'type'             => 'success',
                ];
            }
        }
        return $result;
    }

    protected static function call_post($url, $post) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $reply = curl_exec($ch);
        curl_close($ch);
        return $reply;
    }

}