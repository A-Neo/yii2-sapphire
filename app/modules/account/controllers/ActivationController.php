<?php

namespace app\modules\account\controllers;

use app\components\Api;
use app\models\Activation;
use app\models\Balance;
use app\models\History;
use app\models\User;
use Yii;

class ActivationController extends Controller
{

    public function actionIndex($id = null) {
        /**
         * @var User $user
         */
        $user = Yii::$app->user->identity;
        $clone = 0;
        if($id){
            $user1 = User::findOne(['id' => $id]);
            $iids = $ids = User::find()->where(['referrer_id' => $user->id, 'status' => User::STATUS_ACTIVE])->select('id')->column();
            while(!empty($iids)){
                $iids = User::find()->where(['referrer_id' => $iids, 'status' => User::STATUS_ACTIVE])->andWhere(['NOT', ['id' => $ids]])->select('id')->column();
                $ids = array_merge($ids, $iids);
                /*if(count($ids) > 1000){
                    break;
                }*/
            }
            if(empty($user1) || !in_array($user1->id, $ids) || $user1->isActive()){
                Yii::$app->session->addFlash('success', Yii::t('account', 'Partner not found or not yours referral or already activated.'));
                Yii::$app->session->close();
                return $this->redirect(['/pm/default/index']);
            }
        }
        if($user->isActive() && empty($user1)){
            $clones = $user->getClones(1, 1);
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
            $activationAmount = $clone ? Yii::$app->settings->get('system', 'promotionAmount1') : Yii::$app->settings->get('system', 'activationAmount');
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
                    'table'        => 1,
                    'from_user_id' => $user->id,
                ]);
                $balance->save();
                $activation = new Activation();
                $activation->setAttributes([
                    'user_id' => !empty($user1) ? $user1->id : $user->id,
                    'table'   => 1,
                    'status'  => 1,
                    'clone'   => $clone,
                ]);
                $activation->buy = 1;
                $activation->save();
                Yii::$app->session->addFlash('success', Yii::t('account', 'Successfully activated'));
                if($id && $user1){
                    return $this->redirect(['/pm/partner/index', 'search' => $user1->username]);
                }else{
                    return $this->redirect(['/pm/network/index', 'id' => 1]);
                }
            }else{
                if(empty(Yii::$app->params['api']['account'])){
                    return $this->redirect(['/pm/activation/index']);
                }
                $tx = Yii::$app->request->post('Tx');
                $has = number_format($user->balance - $user->accumulation, 2, '.', '');
                if($has < $activationAmount && (empty($tx['id']) || empty($tx['amount']))){
                    return $this->redirect(['/pm/activation/index']);
                }
                $id = empty($tx['id']) ? 0 : intval($tx['id']);
                $amount = empty($tx['amount']) ? 0 : floatval($tx['amount']);
                if($id){
                    if(!History::saveRefill($id, $amount, $user)){
                        return $this->redirect(['/pm/activation/index']);
                    }
                }
                if($has < $activationAmount){
                    Yii::$app->session->addFlash('warning', Yii::t('account', 'Amount not enough for activation'));
                    return $this->redirect(['/pm/activation/index']);
                }
                $balance = new Balance();
                $balance->setAttributes([
                    'type'         => Balance::TYPE_ACTIVATION,
                    'status'       => Balance::STATUS_ACTIVE,
                    'from_amount'  => $activationAmount,
                    'table'        => 1,
                    'from_user_id' => $user->id,
                ]);
                $balance->save();
                $activation = new Activation();
                $activation->setAttributes([
                    'user_id' => !empty($user1) ? $user1->id : $user->id,
                    'table'   => 1,
                    'status'  => 1,
                    'clone'   => $clone,
                ]);
                $activation->buy = 1;
                $activation->save();
                Yii::$app->session->addFlash('success', Yii::t('account', 'Successfully activated'));
                if($id && $user1){
                    return $this->redirect(['/pm/partner/index', 'search' => $user1->username]);
                }else{
                    return $this->redirect(['/pm/network/index', 'id' => 1]);
                }
            }
        }
        return $this->render('index', ['clone' => $clone, 'user1' => !empty($user1) ? $user1 : null]);
    }
}
