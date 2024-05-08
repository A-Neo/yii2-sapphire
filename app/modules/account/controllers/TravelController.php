<?php

namespace app\modules\account\controllers;

use app\models\TravelDelay;
use app\models\TravelMain;
use app\models\User;
use hail812\adminlte3\assets\AdminLteAsset;
use Yii;

class TravelController extends Controller
{

    public $user_id;

    public function beforeAction($action) {        
        parent::beforeAction($action);
        if (!isset(Yii::$app->user->identity) || Yii::$app->user->identity == null) {
            return false;
        }
        $this->user_id = Yii::$app->user->identity->getId();
        return true;
    }

    public function actionIndex() {

        return $this->render('index',
            [
                'levels'   => TravelMain::getLevels($this->user_id),
                'username' => Yii::$app->user->identity->username,
                'userid' => $this->user_id,
                'delayUsers' => TravelDelay::find()->where(['id_ref' => $this->user_id])->all(),
            ]
        );
    }
    public function actionInit() {

        $user = User::getCurrent();

        $result = TravelMain::initUser($this->user_id);

        if ($result === true) {
            Yii::$app->session->setFlash('okmessage', 'Уровень активирован');
        } else {
            Yii::$app->session->setFlash('errmessage', 'Ошибка активации: ' . $result);
        }

        return $this->redirect(['/pm/travel']);
    }

    public function actionNetList($uid, $level)
    {
        $user = User::findOne(['id' => $uid]);
        if (!$user) {
            return 'User not found!';
        }
        return $this->renderPartial('list',
             [
                 'levels'   => TravelMain::getLevels($user->id),
                 'username' => $user->username,
                 'userid'   => $user->id,
                 'level'    => (int)$level,
             ]
        );
    }

    public function actionUserAvatar($uid)
    {
        $uid = (int)$uid;

        $avatar = Yii::$app->cache->getOrSet('travel_user_avatar_' . $uid, function() use ($uid) {
            $user = User::findOne(['id' => $uid]);
            if ($user && $user->avatar) {
                return $user->avatar;
            }
            return file_get_contents(Yii::getAlias('@webroot') . '/img/noavatar.jpg');
        }, 60);

        return Yii::$app->response->sendContentAsFile($avatar, 'user-avatar-' . $uid . '.jpg', ['inline' => true]);
    }

}
