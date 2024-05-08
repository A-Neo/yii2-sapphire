<?php

namespace app\modules\account\controllers;

use app\models\Activation;
use app\models\User;
use Yii;

class NetworkController extends Controller
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
        $this->layout = 'network';
        return true;
    }

    public function actionIndex($id = null, $search = null) {
        $user = $uu = Yii::$app->user->identity;
        if($search){
            $user = User::find()->where(['username' => $search])->one();
            if(!$user || ($user->status !== User::STATUS_ACTIVE)){
                return $this->redirect(['/pm/network/index', 'id' => $id]);
            }
        }
        if($id && ($id < 1 || $id > 6)){
            return $this->redirect(['/pm/network/index']);
        }
        if(!$id && ($activation = $user->getActiveActivations()->orderBy(['table' => SORT_DESC, 'clone' => SORT_ASC])->one())){
            $activation->checkTx($activation);
            //$activation->checkSetAuto($activation);
            $id = $activation->table;
        }
        if(!User::getCurrent()->isActive()){
            $this->layout = 'main';
            return $this->render('index1');
            //return $this->redirect(['/pm/network/index']);
        }
        for($n = 1; $n <= 6; $n++){
            foreach($user->activeActivations as $activation){
                if($activation->table < $n || $activation->start > $n || $activation->status != Activation::STATUS_ACTIVE){
                    continue;
                }
                if(!$activation->isUsed($n)){
                    $activation->checkSetAuto($activation);
                }
            }
        }
        foreach($user->referrals as $referral){
            if($referral->id == $user->id || $referral->status != User::STATUS_ACTIVE) continue;
            $activations = $referral->activeActivations;
            for($n = 1; $n <= 6; $n++){
                foreach($activations as $activation){
                    if($activation->table < $n || $activation->start > $n){
                        continue;
                    }
                    if(!$activation->isUsed($n)){
                        $activation->checkSetAuto($activation);
                        if($user->id == 1){
                            $activation->refresh();
                            if(!$activation->isUsed($n)){
                                $activation->createRootClone($n);
                                $activation->checkSetAuto($activation);
                            }
                        }
                    }
                }
            }
        }
        $inStructures = $user->id == $uu->id || $user->referrer_id == $uu->id || Yii::$app->user->can('admin');
        if(!$inStructures){
            foreach($user->activeActivations as $a){
                $u = $a->user;
                while(!($u->id == $uu->id || $u->referrer_id == $uu->id)){
                    $a = Activation::find()->where(['status' => Activation::STATUS_ACTIVE])->andWhere(['OR', ['t' . $id . '_left' => $a->id], ['t' . $id . '_right' => $a->id]])->andWhere(['>=', 'table', $id])->one();
                    if(empty($a)){
                        break;
                    }
                    $u = $a->user;
                    if($u->id == $uu->id || $u->referrer_id == $uu->id){
                        $inStructures = true;
                        break;
                    }
                    if($u->id){
                        break;
                    }
                }
                if($inStructures){
                    break;
                }
            }
        }
        if(!$inStructures){
            return $this->redirect(['/pm/network/index']);
        }
        return $this->render('index', ['id' => $id, 'user' => $user, 'search' => $search]);
    }

    public function actionSet($id = null, $at = null, $t = 1, $side = 0, $search = null) {
        $user = Yii::$app->user->identity;
        $user->set($id, $at, $t, $side);
        return $this->redirect(['/pm/network/index', 't' => $id, 'search' => $search]);
    }

}
