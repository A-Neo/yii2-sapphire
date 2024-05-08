<?php

namespace app\models;

use app\components\Api;
use app\components\PmApi;
use Yii;

/**
 * This is the model class for table "{{%history}}".
 *
 * @property int         $id
 * @property int         $user_id
 * @property string|null $date
 * @property string|null $type
 * @property string|null $status
 * @property string|null $from
 * @property float|null  $debitedAmount
 * @property string|null $debitedCurrency
 * @property string|null $to
 * @property float|null  $creditedAmount
 * @property string|null $creditedCurrency
 * @property float|null  $payeerFee
 * @property float|null  $gateFee
 * @property float|null  $exchangeRate
 * @property string|null $currency
 * @property string|null $protect
 * @property string|null $comment
 * @property string|null $isApi
 *
 * @property User        $user
 */
class History extends \yii\db\ActiveRecord
{

    const CURRENCY_PAYEER  = 'Payeer';
    const CURRENCY_PERFECT = 'Perfect Money';
    const CURRENCY_TETHER = 'Tether (TRC20)';
    const CURRENCY_TRX = 'TRX (tron)';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%history}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'user_id'], 'integer'],
            [['date'], 'safe'],
            [['debitedAmount', 'creditedAmount', 'payeerFee', 'gateFee', 'exchangeRate'], 'number'],
            [['debitedAmount', 'creditedAmount', 'payeerFee', 'gateFee', 'exchangeRate', 'debitedCurrency', 'creditedCurrency', 'protect', 'isApi'], 'default', 'value' => null],
            [['type', 'status', 'from', 'to'], 'filter', 'filter' => function ($value) {
                return mb_substr($value, 0, 32);
            }],
            [['type', 'status', 'from', 'to', 'currency'], 'string', 'max' => 32],
            [['debitedCurrency', 'creditedCurrency', 'protect', 'isApi'], 'filter', 'filter' => function ($value) {
                return mb_substr($value, 0, 256);
            }],
            [['debitedCurrency', 'creditedCurrency', 'protect', 'isApi'], 'string', 'max' => 8],
            [['comment'], 'filter', 'filter' => function ($value) {
                return mb_substr($value, 0, 256);
            }],
            [['comment'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id'               => Yii::t('site', 'ID'),
            'user_id'          => Yii::t('site', 'User'),
            'date'             => Yii::t('site', 'Date'),
            'type'             => Yii::t('site', 'Type'),
            'status'           => Yii::t('site', 'Status'),
            'from'             => Yii::t('site', 'Sender'),
            'debitedAmount'    => Yii::t('site', 'Debited Amount'),
            'debitedCurrency'  => Yii::t('site', 'Debited Currency'),
            'to'               => Yii::t('site', 'Recipient'),
            'creditedAmount'   => Yii::t('site', 'Sum'),
            'creditedCurrency' => Yii::t('site', 'Currency'),
            'payeerFee'        => Yii::t('site', 'Fee'),
            'gateFee'          => Yii::t('site', 'Gate Fee'),
            'exchangeRate'     => Yii::t('site', 'Exchange Rate'),
            'protect'          => Yii::t('site', 'Protect'),
            'comment'          => Yii::t('site', 'Comment'),
            'currency'         => Yii::t('site', 'Currency'),
            'isApi'            => Yii::t('site', 'Is Api'),
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function saveRefill($id, $amount, $user, $flash = true, $currency = History::CURRENCY_PAYEER) {
        $history = History::findOne(['id' => $id, 'currency' => $currency]);
        if(empty($history)){
            if($currency == History::CURRENCY_PAYEER){
                Api::updateHistory();
            }
            if($currency == History::CURRENCY_PERFECT){
                PmApi::updateHistory();
            }
            if($currency == History::CURRENCY_TETHER || $currency == History::CURRENCY_TRX){
                if($amount >= 9) {
                    $tx = Yii::$app->request->post('Tx');
                    
                    $b = Balance::find()->where(['type' => Balance::TYPE_REFILL, 'comment' => $tx['hash']])->asArray()->one();
                    if($b) {
                        if($b['status'] != Balance::STATUS_WAITING) {
                            if($flash){
                                Yii::$app->session->addFlash('error', 'Транзакция уже была одобрена или отклонена');
                            }
                            return false;
                        }
                        if($b['to_user_id'] != $user->id) {
                            if($flash){
                                Yii::$app->session->addFlash('error', Yii::t('account', 'Transaction {id} has different target wallet', ['id' => $tx['hash']]) . '. ' . Yii::t('account', 'Please check and try again'));
                            }
                            return false;
                        } else {
                            if($flash){
                                Yii::$app->session->addFlash('error', 'Транзакция уже находится в обработке, пожалуйста дождитесь ее успешной проверки.');
                            }
                            return false;
                        }
                    } else {
                        $history = new History();
                        $history->setAttributes([
                            'user_id '          => $user->id,
                            'date'              => date('Y-m-d H:i:s', time()),
                            'status'            => Balance::STATUS_WAITING,
                            'type'              => Balance::TYPE_REFILL,
                            'currency'          => $currency,
                            'creditedCurrency'  => 'TRX',
                            'creditedAmount'    => $amount,
                            'isApi'             => 'N'
                        ]);
                        
                        if ($history->save()) {
                            $balance = new Balance();
                            // $balance->
                            $balance->setAttributes([
                                'type'       => Balance::TYPE_REFILL,
                                'status'     => Balance::STATUS_WAITING,
                                'to_amount'  => $amount,
                                'to_user_id' => $user->id,
                                'history_id' => $history->id,
                                //'history_id' => $history->id,
                                'comment' => $_POST['Tx']['hash'],
                            ]);
                            $balance->save();
                            return true;
                        } else {
                            return false;
                        }
                    }
                } else {
                    if($flash){
                        Yii::$app->session->addFlash('error', 'Значение сумма слишком маленькое.');
                    }
                    return false;
                }
            }
            $history = History::findOne(['id' => $id, 'currency' => $currency]);
            if(empty($history)){
                if($flash){
                    Yii::$app->session->addFlash('error', Yii::t('account', 'Transaction {id} not found', ['id' => $id]) . '. ' . Yii::t('account', 'Please check and try again'));
                }
                return false;
            }
        }
        if(($history->to != Yii::$app->params['api']['account'] && $currency == History::CURRENCY_PAYEER)
            || ($history->to != Yii::$app->params['api']['pm_account'] && $currency == History::CURRENCY_PERFECT)){
            if($flash){
                Yii::$app->session->addFlash('error', Yii::t('account', 'Transaction {id} has different target wallet', ['id' => $id]) . '. ' . Yii::t('account', 'Please check and try again'));
            }
            return false;
        }
        if($history->creditedAmount != $amount){
            if($flash){
                Yii::$app->session->addFlash('error', Yii::t('account', 'Transaction {id} has different amount than you enter', ['id' => $id]) . '. ' . Yii::t('account', 'Please check and try again'));
            }
            return false;
        }
        if(!empty($history->user_id)){
            if($flash){
                Yii::$app->session->addFlash('error', Yii::t('account', 'Transaction {id} already applied', ['id' => $id]) . '. ' . Yii::t('account', 'Please check and try again'));
            }
            return false;
        }
        $history->user_id = $user->id;
        if(!$history->save()){
            if($flash){
                Yii::$app->session->addFlash('error', Yii::t('account', 'Error on save transaction {id}', ['id' => $id]) . '. ' . Yii::t('account', 'Please check and try again'));
            }
            return false;
        }
        $balance = new Balance();
        $balance->setAttributes([
            'type'       => Balance::TYPE_REFILL,
            'status'     => Balance::STATUS_ACTIVE,
            'to_amount'  => $history->creditedAmount,
            'to_user_id' => $user->id,
            'history_id' => $history->id,
        ]);
        $balance->save();
        if($flash){
            Yii::$app->session->addFlash('success', Yii::t('account', 'Amount successfully added to you balance'));
        }
        return true;
    }

}
