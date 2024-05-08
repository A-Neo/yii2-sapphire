<?php

namespace app\modules\account\controllers;

use app\models\ActivationForever;
use app\models\User;
use Yii;
use yii\helpers\VarDumper;

class ForeverController extends Controller
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

    public function actionIndex($id = null, $search = null) {
        $user = $uu = Yii::$app->user->identity;
        if($search){
            $user = User::find()->where(['username' => $search])->one();
            if(!$user || ($user->status !== User::STATUS_ACTIVE)){
                return $this->redirect(['/pm/forever/index', 'id' => $id]);
            }
        }
        if($id && ($id < 1 || $id > 4)){
            return $this->redirect(['/pm/forever/index']);
        }
        if(!$id && ($activation = $user->getActiveActivationsForever()->orderBy(['table' => SORT_DESC, 'clone' => SORT_ASC])->one())){
            $activation->checkTx($activation);
            //$activation->checkSetAuto($activation);
            $id = $activation->table;
        }
        if(!$user->isActiveForever($id)){
            $this->layout = 'index';
            return $this->render('index1');
            //return $this->redirect(['/pm/forever/index']);
        }
        for($n = 1; $n <= 4; $n++){
            foreach($user->activeActivationsForever as $activation){
                if($activation->table < $n || $activation->start > $n || $activation->status != ActivationForever::STATUS_ACTIVE){
                    continue;
                }
                if(!$activation->isUsed($n)){
                    $activation->checkSetAuto($activation);
                }
            }
        }
        foreach($user->referrals as $referral){
            if($referral->id == $user->id || $referral->status != User::STATUS_ACTIVE) continue;
            $activations = $referral->activeActivationsForever;
            for($n = 1; $n <= 4; $n++){
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
            foreach($user->activeActivationsForever as $a){
                $u = $a->user;
                while(!($u->id == $uu->id || $u->referrer_id == $uu->id)){
                    $a = ActivationForever::find()->where(['status' => ActivationForever::STATUS_ACTIVE])->andWhere(['OR', ['t' . $id . '_left' => $a->id], ['t' . $id . '_right' => $a->id]])->andWhere(['>=', 'table', $id])->one();
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
            return $this->redirect(['/pm/forever/index']);
        }
//        VarDumper::dump($inStructures, 5, true);die;
        return $this->render('index', ['id' => $id, 'user' => $user, 'search' => $search]);
    }

    public function actionSet($id = null, $at = null, $t = 1, $side = 0, $search = null) {
        $user = Yii::$app->user->identity;
        $user->setForever($id, $at, $t, $side);
        return $this->redirect(['/pm/forever/index', 't' => $id, 'search' => $search]);
    }


}
