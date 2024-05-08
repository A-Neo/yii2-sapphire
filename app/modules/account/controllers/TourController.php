<?php

namespace app\modules\account\controllers;

use app\assets\AccountAsset;
use app\models\Balance;
use app\models\Tour;
use app\models\User;
use Yii;
use yii\web\UploadedFile;

class TourController extends Controller
{

    public function actionIndex($id = null) {
        /**
         * @var $user User
         */
        $user = Yii::$app->user->identity;
        $new = false;
        if($id){
            $model = $user->getTours()->andWhere(['id' => $id])->one();
        }
        if(empty($model)){
            $new = true;
            $model = new Tour();
            $model->user_id = $user->id;
            $model->whatsapp = $user->phone;
        }
        if($this->request->isPost && (!$new || ($user->sapphire_personal >= 3 && $user->sapphire_partners >= 6)) && $model->status != Tour::STATUS_CONFIRMED){
            if($model->load($this->request->post())){
                $passport = UploadedFile::getInstance($model, 'passport');
                if($passport && $passport->size && $passport->error == 0){
                    $model->passport = file_get_contents($passport->tempName);
                    unlink($passport->tempName);
                }else{
                    $model->passport = $model->getOldAttribute('passport');
                }
                if($model->save()){
                    if($new){
                        $b = new Balance();
                        $b->setAttributes([
                            'type'          => Balance::TYPE_TOUR,
                            'status'        => Balance::STATUS_ACTIVE,
                            'table'         => 6,
                            'from_user_id'  => $user->id,
                            'from_sapphire' => $model->tourName->price,
                        ]);
                        $b->save();
                    }
                    return $this->redirect(['index']);
                }
            }
        }
        return $this->render('index', ['model' => $model]);
    }

    public function actionPassport($id) {
        /**
         * @var $user User
         */
        $user = Yii::$app->user->identity;
        $model = $user->getTours()->andWhere(['id' => $id])->one();
        $as = new AccountAsset();
        $this->layout = false;
        if($model && $model->passport){
            return Yii::$app->response->sendContentAsFile($model->passport, 'tour-passport-' . Yii::$app->user->id . '.jpg', ['inline' => true]);
        }else{
            return $this->renderContent('');
        }
    }
}
