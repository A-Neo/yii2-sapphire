<?php

namespace app\modules\account\controllers;

use app\models\Activation;
use app\models\Balance;
use app\models\EmeraldDelay;
use app\models\EmeraldMain;
use app\models\ErmeraldOrder;
use app\models\forms\OrderForm;
use app\models\Page;
use app\models\Payout;
use app\models\search\PayoutSearch;
use app\models\User;
use Yii;
use yii\helpers\Url;

use app\helpers\FunctionHelper as Help;

class EmeraldController extends Controller
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
//        Help::dd($this->user_id);
        $model = new EmeraldMain();

        $user = User::findOne(['id' => $this->user_id]);
        $user->balance = 100;
        if (!$user->save()) {
            var_dump($user->errors);
            die;
        }

        //Help::dd($user);
        //Help::dd( $model::getLastLevels() );
        return $this->render('index',
            [
                'levels'   => EmeraldMain::find()->where(['id_user' => $this->user_id])->orderBy(['level' => SORT_ASC])->indexBy('level')->all(),
                'username' => Yii::$app->user->identity->username,
                'user' => $this->user,
                'userid' => $this->user_id,
                'delayUsers' => EmeraldDelay::find()->where(['id_ref' => $this->user_id])->all(),
                'model' => $model,
            ]
        );
    }

    public function actionInit() {

        if (Yii::$app->request->isPost) {
            $emeraldMain = Yii::$app->request->post('DynamicModel');
            $ref_user = User::findOne(['username' => $emeraldMain['id_ref']]);
        }

        $user = User::getCurrent();


        $result = EmeraldMain::initUser($this->user_id, $ref_user);

        if ($result === true) {
            Yii::$app->session->setFlash('okmessage', 'Уровень активирован');
        } else {
            Yii::$app->session->setFlash('errmessage', 'Ошибка активации: ' . $result);
        }

        return $this->redirect(['pm/emerald/form']);
    }

    public function actionPassive() {

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

        return $this->render('passive',
            [
                'user' => $this->user,
                'searchModel'  => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    public function actionForm($ret = null) {
        $model = new OrderForm();
        if (!Yii::$app->request->isGet) {
            $order = new \app\models\EmeraldOrder();
            if ($order->formSave(Yii::$app->request->post())) {
                return $this->redirect(['index']);
            }
            return true;
        } else {

            $product = ['id' => 1, 'name' => 'Пластырь'];
            return $this->render('form',
                [
                    'user_id' => $this->user_id,
                    'product' => $product,
                    'model' => $model,
                ]
            );
        }

    }
    public function actionNetList($uid, $level)
    {
        $user = User::findOne(['id' => $uid]);
        if (!$user) {
            return 'User not found!';
        }
        return $this->renderPartial('list',
            [
                'levels'   => EmeraldMain::getLevels($user->id),
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
