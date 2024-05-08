<?php

namespace app\modules\account\controllers;

use app\models\search\UserSearch;
use app\models\User;
use Yii;

class PartnerForeverController extends Controller
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

    public function actionIndex($ref = null, $search = null) {
        $user = Yii::$app->user->identity;
        $dataProvider = null;
        if($search){
            $searchModel = new UserSearch();
            $iids = $ids = User::find()->where(['referrer_id' => $user->id, 'status' => User::STATUS_ACTIVE])->select('id')->column();
            while(!empty($iids)){
                $iids = User::find()->where(['referrer_id' => $iids, 'status' => User::STATUS_ACTIVE])->andWhere(['NOT', ['id' => $ids]])->select('id')->column();
                $ids = array_merge($ids, $iids);
                /*if(count($ids) > 1000){
                    break;
                }*/
            }
            $dataProvider = $searchModel->search([
                'UserSearch' => [
                    'id'       => $ids,
                    'username' => $search,
                ],
            ]);
        }else if($ref){
            $user = User::findIdentity($ref);
            if(empty($user)){
                return $this->redirect(['/pm/partner/index']);
            }
        }
        return $this->render('index', [
            'user'         => $user,
            'dataProvider' => $dataProvider,
            'search'       => $search,
        ]);
    }

}
