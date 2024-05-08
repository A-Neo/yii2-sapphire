<?php

namespace app\modules\account\controllers;

use hail812\adminlte3\assets\AdminLteAsset;
use Yii;
use app\models\forms\ProfileForm;
use app\models\forms\ProfileFinPasswordForm;
use app\models\forms\ProfilePasswordForm;
use app\models\forms\ProfileSettingForm;
use yii\helpers\VarDumper;

/**
 * ProfileController
 */
class ProfileController extends Controller
{

    public function actionIndex() {
        return $this->render('index', [
            'user' => Yii::$app->user->identity,
        ]);
    }

    public function actionEdit() {
        $model = new ProfileForm();
        if($this->request->isPost){
            if($model->load($this->request->post()) && $model->save()){
                return $this->redirect(['index']);
            }
        }
        return $this->render('edit', [
            'model' => $model,
        ]);
    }

    public function actionPassword() {
        $model = new ProfilePasswordForm();
        if($this->request->isPost){
            if($model->load($this->request->post()) && $model->save()){
                return $this->redirect(['index']);
            }
        }
        return $this->render('password', [
            'model' => $model,
        ]);
    }

    public function actionFinPasswordCode() {
        $model = new ProfileFinPasswordForm();
        $model->generateNewCode();
        Yii::$app->session->addFlash('success', Yii::t('account', 'Confirmation code was sent to your email'));
        return $this->redirect(['index/fin-password']);
    }

    public function actionFinPassword() {
        $model = new ProfileFinPasswordForm();
        if($this->request->isPost){
            if($model->load($this->request->post()) && $model->save()){
                return $this->redirect(['index']);
            }
        }
        return $this->render('fin-password', [
            'model' => $model,
        ]);
    }

    public function actionLink() {

        return $this->render('link');
        //Проверка активации юзера
        if(Yii::$app->user->identity->isActive() || Yii::$app->user->identity->isActiveForever()) {
            return $this->render('link');

        }
        else {
            Yii::$app->session->addFlash('danger', Yii::t('account', 'Activation required'));
            Yii::$app->session->close();
            $this->redirect(['/pm/network/index']);
            return false;
        }

    }

    public function actionAvatar() {
        $as = new AdminLteAsset();
        if(Yii::$app->user->identity->avatar){
            return Yii::$app->response->sendContentAsFile(Yii::$app->user->identity->avatar, 'user-avatar-' . Yii::$app->user->id . '.jpg', ['inline' => true]);
        }else{
            return Yii::$app->response->sendFile(Yii::getAlias($as->sourcePath . '/img/user2-160x160.jpg'), 'user2-160x160.jpg', ['inline' => true]);
        }
    }

}