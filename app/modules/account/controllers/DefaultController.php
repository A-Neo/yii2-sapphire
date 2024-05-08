<?php

namespace app\modules\account\controllers;

use app\models\User;
use Yii;

class DefaultController extends Controller
{

    public function actionIndex() {
        return $this->render('index');
    }

    public function actionBack() {
        if(Yii::$app->session->has('back-to-admin')){
            $user = User::findOne(['id' => Yii::$app->session->has('back-to-admin')]);
            if(in_array($user->role, [User::ROLE_ADMIN, User::ROLE_MODERATOR])){
                Yii::$app->user->logout(true);
                Yii::$app->user->login($user, 3600 * 24 * 30);
                return $this->redirect(['/cp/user/index']);
            }
        }
        return $this->redirect(['/pm/default/index']);
    }

}
