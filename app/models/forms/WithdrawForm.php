<?php

namespace app\models\forms;

use app\models\Withdraw;
use Yii;
use yii\base\Model;
use app\models\User;
use yii\helpers\Url;

class WithdrawForm extends Model
{

    public $amount1;
    public $amount2;

    public function rules() {
        /**
         * @var $user User
         */
        $user = Yii::$app->user->identity;
        $settings = Yii::$app->settings;
        $exists1 = Withdraw::find()->where(['user_id' => $user->id, 'coin' => 1, 'status' => Withdraw::STATUS_WAITING])->select('SUM(amount)')->scalar();
        $exists2 = Withdraw::find()->where(['user_id' => $user->id, 'coin' => 2, 'status' => Withdraw::STATUS_WAITING])->select('SUM(amount)')->scalar();
        return [
            [['amount1'], 'required', 'when' => function ($model) {
                return empty($model->amount2);
            }],
            [['amount2'], 'required', 'when' => function ($model) {
                return empty($model->amount1);
            }],
            [['amount1'], 'integer', 'min' => $settings->get('system', 'MinWithdrawAmount1'), 'max' => $user->balance1 - $exists1,
             'tooBig'                      => Yii::t('site', 'Not enough balance')],
            [['amount2'], 'integer', 'min' => $settings->get('system', 'MinWithdrawAmount2'), 'max' => $user->balance2 - $exists2,
             'tooBig'                      => Yii::t('site', 'Not enough balance')],
        ];
    }

    public function beforeValidate() {
        $user = Yii::$app->user->identity;
        if (!empty($this->amount1) && empty($user->wallet1)) {
            $this->addError('amount1', Yii::t('account', 'You must setup wallet address for withdrawing first'));
        }
        if (!empty($this->amount2) && empty($user->wallet2)) {
            $this->addError('amount2', Yii::t('account', 'You must setup wallet address for withdrawing first'));
        }
        return parent::beforeValidate();
    }

    public function attributeLabels() {
        return [
            'amount1' => Yii::t('site', 'Amount'),
            'amount2' => Yii::t('site', 'Amount'),
        ];
    }

    public function save() {
        if ($this->validate()) {
            $user = Yii::$app->user->identity;
            if ($this->amount1 > 0) {
                $coin = 1;
                $amount = $this->amount1;
            } else {
                $coin = 2;
                $amount = $this->amount2;
            }
            $exists = Withdraw::find()->where(['user_id' => $user->id, 'coin' => $coin, 'status' => Withdraw::STATUS_WAITING])->select('SUM(amount)')->scalar();
            if ($exists > 0 && $amount > $user->{'balance' . $coin} - $exists) {
                $this->addError('amount' . $coin, Yii::t('site', 'Not enough balance'));
                return false;
            }
            $model = new Withdraw();
            $model->setAttributes([
                'user_id' => $user->id,
                'coin'    => $coin,
                'amount'  => $amount,
                'status'  => Withdraw::STATUS_WAITING,
            ]);
            if (!$model->save()) {
                $this->addError('amount' . $coin, implode('. ', $model->getErrorSummary(true)));
                return false;
            }
            return true;
        }
        return false;
    }

}