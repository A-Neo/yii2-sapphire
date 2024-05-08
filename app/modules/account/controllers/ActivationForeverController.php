<?php

namespace app\modules\account\controllers;

use app\components\Api;
use app\models\ActivationForever;
use app\models\Balance;
use app\models\History;
use app\models\User;
use Yii;
use yii\helpers\VarDumper;

class ActivationForeverController extends Controller
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
        $this->layout = 'index';
        return true;
    }


    public function actionIndex($id = null) {//user id
        /**
         * @var User $user
         */
        // Yii::$app->cache->flush();
        $user = Yii::$app->user->identity;
        $clone = 0;
        if($id){//user id
            $userPartner = User::findOne(['id' => $id]);
            $iids = $ids = User::find()
                ->where(['referrer_id' => $user->id, 'status' => User::STATUS_ACTIVE])
                ->select('id')
                ->column();
            while(!empty($iids)){
                $iids = User::find()->where(['referrer_id' => $iids, 'status' => User::STATUS_ACTIVE])->andWhere(['NOT', ['id' => $ids]])->select('id')->column();
                $ids = array_merge($ids, $iids);
                /*if(count($ids) > 1000){
                    break;
                }*/
            }
//            VarDumper::dump($ids, 5, true);die;
            if(empty($userPartner) || !in_array($userPartner->id, $ids) || $userPartner->isActiveForever()){
                Yii::$app->session->addFlash('success', Yii::t('account', 'Partner not found or not yours referral or already activated.'));
                Yii::$app->session->close();
                return $this->redirect(['/pm/default/index']);
            }
        }
        if($user->isActiveForever() && empty($userPartner)){
            $clones = $user->getClones(1, 1);
//            VarDumper::dump($user->isActiveForever(), 5, true);die;
            $c = count($clones);
            if($c < 2){
                $clone = $c + 1;
            }else{
                Yii::$app->session->addFlash('success', Yii::t('account', 'Already activated'));
                Yii::$app->session->close();
                return $this->redirect(['/pm/default/index']);
            }
        }
        if(Yii::$app->request->isPost){
            $activationAmount = $clone ? Yii::$app->settings->get('system', 'promotionAmountForever1') : Yii::$app->settings->get('system', 'activationAmountForever');
            if(TEST_MODE){
                $tx = Yii::$app->request->post('Tx');
                $amount = empty($tx['amount']) ? 0 : floatval($tx['amount']);
                if(($amount < $activationAmount) && ($user->balance - $user->accumulation < $activationAmount)){
                    $amount = $activationAmount;
                }
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
                $balance = new Balance();
                $balance->setAttributes([
                    'type'         => Balance::TYPE_ACTIVATION,
                    'status'       => Balance::STATUS_ACTIVE,
                    'from_amount'  => $activationAmount,
                    'table'        => 11,
                    'from_user_id' => $user->id,
                ]);
                $balance->save();
                $activation = new ActivationForever();
                $activation->setAttributes([
                    'user_id' => !empty($userPartner) ? $userPartner->id : $user->id,
                    'table'   => 1,
                    'status'  => 1,
                    'clone'   => $clone,
                ]);
                $activation->buy = 1;
                $activation->save();
                Yii::$app->session->addFlash('success', Yii::t('account', 'Successfully activated'));
                if($id && $userPartner){
                    return $this->redirect(['/pm/partner/index', 'search' => $userPartner->username]);
                }else{
                    return $this->redirect(['/pm/forever/index', 'id' => 1]);
                }
            }
            else{
                if(empty(Yii::$app->params['api']['account'])){
                    return $this->redirect(['/pm/activation-forever/index']);
                }
                $tx = Yii::$app->request->post('Tx');
                $has = number_format($user->balance - $user->accumulation, 2, '.', '');
                if($has < $activationAmount && (empty($tx['id']) || empty($tx['amount']))){
                    return $this->redirect(['/pm/activation-forever/index']);
                }
                $id = empty($tx['id']) ? 0 : intval($tx['id']);
                $amount = empty($tx['amount']) ? 0 : floatval($tx['amount']);
                if($id){
                    if(!History::saveRefill($id, $amount, $user)){
                        return $this->redirect(['/pm/activation-forever/index']);
                    }
                }
                if($has < $activationAmount){
                    Yii::$app->session->addFlash('warning', Yii::t('account', 'Amount not enough for activation'));
                    return $this->redirect(['/pm/activation-forever/index']);
                }
                $balance = new Balance();
                $balance->setAttributes([
                    'type'         => Balance::TYPE_ACTIVATION,
                    'status'       => Balance::STATUS_ACTIVE,
                    'from_amount'  => $activationAmount,
                    'table'        => 11,
                    'from_user_id' => $user->id,
                ]);
                $balance->save();
                $activation = new ActivationForever();
                $activation->setAttributes([
                    'user_id' => !empty($userPartner) ? $userPartner->id : $user->id,
                    'table'   => 1,
                    'status'  => 1,
                    'clone'   => $clone,
                ]);
                $activation->buy = 1;
                $activation->save();
                Yii::$app->session->addFlash('success', Yii::t('account', 'Successfully activated'));
                if($id && $userPartner){
                    return $this->redirect(['/pm/partner/index', 'search' => $userPartner->username]);
                }else{
                    return $this->redirect(['/pm/forever/index', 'id' => 1]);
                }
            }
        }
                $this->layout = 'network';

       // VarDumper::dump($clone, 5, true);die;
        return $this->render('index', ['clone' => $clone, 'user1' => !empty($userPartner) ? $userPartner : null]);
    }
}
