<?php

namespace app\modules\account\controllers;

use app\components\Api;
use app\models\Balance;
use app\models\enumerables\SettingType;
use app\models\History;
use app\models\Payout;
use app\models\search\PayoutSearch;
use app\models\User;
use Yii;
use app\models\search\BalanceSearch;

class BalanceController extends Controller
{

    public function beforeAction($action) {
        if(!parent::beforeAction($action)){
            return false;
        }
        /*if(!Yii::$app->user->identity->isActive()){
            Yii::$app->session->addFlash('danger', Yii::t('account', 'Activation required'));
            Yii::$app->session->close();
            $this->redirect(['/pm/default/index']);
            return false;
        }*/
        return true;
    }

    public function actionIndex() {
        if(Yii::$app->request->isPost){
            $user = User::getCurrent();
            $tx = Yii::$app->request->post('Tx');
            $comment = empty($tx['comment']) ? '' : strip_tags(trim($tx['comment']));
            $hasPoints = Balance::find()->where(['status' => Balance::STATUS_WAITING, 'comment' => ''])->exists();
            if($hasPoints && $comment){
                Balance::updateAll(['comment' => $comment], ['to_user_id' => $user->id, 'status' => Balance::STATUS_WAITING, 'comment' => '']);
            }
        }
        $searchModel = new BalanceSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->query->andWhere(['or', ['from_user_id' => Yii::$app->user->id], ['to_user_id' => Yii::$app->user->id]])
            ->andWhere(['NOT', ['AND', ['type' => [Balance::TYPE_CHARGING, Balance::TYPE_ACCUMULATION]], ['NOT', ['to_user_id' => Yii::$app->user->id]]]]);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionRefill($slug = History::CURRENCY_PAYEER) {
        if(Yii::$app->request->isPost){
            $user = Yii::$app->user->identity;
            $tx = Yii::$app->request->post('Tx');
            $amount = empty($tx['amount']) ? 0 : floatval($tx['amount']);
            $currency = empty($tx['currency']) ? $slug : trim($tx['currency']);
            if($amount < 1){
                Yii::$app->session->addFlash('error', Yii::t('account', 'Minimal refill amount {amount}', ['amount' => Api::asNumber(1)]));
                return $this->redirect(['/pm/balance/refill']);
            }
            $id = empty($tx['id']) ? 0 : intval($tx['id']);
            if(TEST_MODE){
                if($amount){
                    $balance = new Balance();
                    $balance->setAttributes([
                        'type'       => Balance::TYPE_REFILL,
                        'status'     => Balance::STATUS_ACTIVE,
                        'to_amount'  => $amount,
                        'to_user_id' => $user->id,
                    ]);
                    $balance->save();
                }
            }else{
                if(empty(Yii::$app->params['api']['account'])){
                    return $this->redirect(['/pm/balance/index']);
                }
                if(!History::saveRefill($id, $amount, $user, true, $currency)){
                    return $this->redirect(['/pm/balance/refill', 'slug' => $currency]);
                }
            }
            return $this->redirect(['/pm/balance/index']);
        }
        return $this->render('refill', ['curr' => $slug]);
    }

    public function actionTransfer() {
        if(Yii::$app->request->isPost){
            /**
             * @var User $user
             */
            $user = Yii::$app->user->identity;
            $tx = Yii::$app->request->post('Tx');
            $amount = empty($tx['amount']) ? 0 : floatval($tx['amount']);
            $to = empty($tx['to']) ? 0 : trim($tx['to']);
            $comment = empty($tx['comment']) ? '' : strip_tags(trim($tx['comment']));
            if($amount < 0.01){
                Yii::$app->session->addFlash('error', Yii::t('account', 'Minimal transfer amount {amount}', ['amount' => Api::asNumber(0.01)]));
                return $this->redirect(['/pm/balance/transfer']);
            }
            if($amount > $user->balance - $user->accumulation){
                Yii::$app->session->addFlash('error', Yii::t('account', 'Not enough amount on balance'));
                return $this->redirect(['/pm/balance/transfer']);
            }
            $toUser = User::find()->where(['username' => $to, 'status' => User::STATUS_ACTIVE])->one();
            if(!$toUser){
                Yii::$app->session->addFlash('error', Yii::t('account', 'User \'{login}\' not found', ['login' => $to]));
                return $this->redirect(['/pm/balance/transfer']);
            }
            $password = empty($tx['password']) ? '' : $tx['password'];
            if(!$user->validatePassword($password, 'fin_password')){
                Yii::$app->session->addFlash('error', Yii::t('account', 'Password invalid'));
                return $this->redirect(['/pm/balance/payout']);
            }
            $fee = Yii::$app->settings->get('system', 'transferFee');
            $fee = ceil($amount * $fee) / 100;
            $value = Yii::$app->settings->get('system', 'transferFeeValue', 0);
            $value += $fee;
            Yii::$app->settings->set('system', 'transferFeeValue', $value, SettingType::FLOAT_TYPE, 2);
            $toAmount = $amount - $fee;
            $balance = new Balance();
            $balance->setAttributes([
                'type'         => Balance::TYPE_TRANSFER,
                'status'       => Balance::STATUS_ACTIVE,
                'from_amount'  => $amount,
                'to_amount'    => $toAmount,
                'from_user_id' => $user->id,
                'to_user_id'   => $toUser->id,
                'comment'      => $comment,
            ]);
            $balance->save();
            Yii::$app->session->addFlash('success', Yii::t('account', 'Successfully transferred'));
            return $this->redirect(['/pm/balance/index']);
        }
        return $this->render('transfer');
    }

    public function actionPayout() {
        $fee = Yii::$app->settings->get('system', 'payoutFee');
        if(Yii::$app->request->isPost){
            /**
             * @var User $user
             */
            $user = Yii::$app->user->identity;
            $tx = Yii::$app->request->post('Tx');
            $amount = empty($tx['amount']) ? 0 : floatval($tx['amount']);
            $password = empty($tx['password']) ? '' : $tx['password'];
            $comment = empty($tx['comment']) ? '' : strip_tags(trim($tx['comment']));
            $walletType = empty($tx['wallet_type']) ? 'payeer' : trim($tx['wallet_type']);
            $comission = 2;
            if($walletType == 'banki_rf') {
                $comission = 6;
            }
            if($walletType == 'dc') {
                $comission = 4;
            }
            if(!$user->validatePassword($password, 'fin_password')){
                Yii::$app->session->addFlash('error', Yii::t('account', 'Password invalid'));
                return $this->redirect(['/pm/balance/payout']);
            }
            if($amount >= 1 && $amount <= $user->balance - $user->accumulation){
                $balance = new Balance();
                $balance->setAttributes([
                    'type'         => Balance::TYPE_PAYOUT,
                    'status'       => Balance::STATUS_ACTIVE,
                    'from_user_id' => $user->id,
                    'from_amount'  => $amount,
                    'comment'      => $comment,
                ]);
                $balance->save();
                $payout = new Payout();
                $payout->setAttributes([
                    'user_id'       => $user->id,
                    'balance_id'    => $balance->id,
                    'amount'        => $amount,
                    'comment'       => $comment,
                    'status'        => Payout::STATUS_INACTIVE,
                    'wallet_type'   => $walletType,
                    'comission'     => $comission
                ]);
                $payout->save();
            }
            return $this->redirect(['/pm/balance/payout']);
        }

        $searchModel = new PayoutSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->query->andWhere(['user_id' => Yii::$app->user->id]);

        return $this->render('payout', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
