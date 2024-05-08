<?php

namespace app\modules\admin\controllers;

use app\components\Api;
use app\models\Activation;
use app\models\Balance;
use app\models\enumerables\SettingType;
use app\models\User;
use app\models\search\UserSearch;
use yii\base\BaseObject;
use yii\web\NotFoundHttpException;
use yii2mod\editable\EditableAction;
use yii2mod\toggle\actions\ToggleAction;
use Yii;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{

    public function actions() {
        return [
            'edit'   => [
                'class'      => EditableAction::class,
                'modelClass' => User::class,
            ],
            'toggle' => [
                'class'      => ToggleAction::class,
                'modelClass' => User::class,
            ],
        ];
    }

    /**
     * Lists all User models.
     *
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate() {
        $model = new User();
        if($this->request->isPost){
            if($model->load($this->request->post())){
                $model->setPassword(trim($model->password));
                $model->generateAuthKey();
                if($model->save()){
                    return $this->redirect(['index']);
                }
            }
        }else{
            $model->loadDefaultValues();
            $model->password = null;
            $model->fin_password_t = null;
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id ID
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        /**
         * @var $model User;
         */
        $model = $this->findModel($id);
        if($this->request->isPost){
            if($model->load($this->request->post())){
                if(!empty(trim($model->password))){
                    $model->setPassword(trim($model->password));
                }
                if(!empty(trim($model->fin_password_t))){
                    $model->setPassword(trim($model->fin_password_t), 'fin_password');
                }
                if($model->save()){
                    return $this->redirect(['index']);
                }else{
                    Yii::$app->session->addFlash('error', implode('<br/>', $model->getErrorSummary(true)));
                }
            }
        }
        $model->password = null;
        $model->fin_password_t = null;
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionExchange($id) {
        $model = $this->findModel($id);
        if($this->request->isPost){
            /**
             * @var User $user
             */
            $user = $model;
            $tx = Yii::$app->request->post('Tx');
            $amount = empty($tx['amount']) ? 0 : floatval($tx['amount']);
            $to = empty($tx['to']) ? 0 : trim($tx['to']);
            if($amount < 1){
                Yii::$app->session->addFlash('error', Yii::t('account', 'Minimal transfer amount {amount}', ['amount' => Api::asNumber(1)]));
                return $this->redirect(['/cp/user/exchange', 'id' => $id]);
            }
            if($amount > $user->balance - $user->accumulation){
                Yii::$app->session->addFlash('error', Yii::t('account', 'Not enough amount on balance'));
                return $this->redirect(['/cp/user/exchange', 'id' => $id]);
            }
            $toUser = User::find()->where(['username' => $to, 'status' => User::STATUS_ACTIVE])->one();
            if(!$toUser){
                Yii::$app->session->addFlash('error', Yii::t('account', 'User \'{login}\' not found', ['login' => $to]));
                return $this->redirect(['/cp/user/exchange', 'id' => $id]);
            }
            $fee = Yii::$app->settings->get('system', 'transferFee');
            $fee = ceil($amount * $fee) / 100;
            $value = Yii::$app->settings->get('system', 'transferFeeValue', 0);
            $value += $fee;
            Yii::$app->settings->set('system', 'transferFeeValue', $value, SettingType::FLOAT_TYPE, 2);
            $toAmount = $amount - $fee;
            $balance = new Balance();
            $balance->setAttributes([
                'type'         => Balance::TYPE_TRANSFER,
                'status'       => Balance::STATUS_ACTIVE,
                'from_amount'  => $amount,
                'to_amount'    => $toAmount,
                'from_user_id' => $user->id,
                'to_user_id'   => $toUser->id,
            ]);
            $balance->save();
            Yii::$app->session->addFlash('success', Yii::t('account', 'Successfully transferred'));
            return $this->redirect(['/cp/user/index']);
        }
        $model->password = null;
        return $this->render('exchange', [
            'model' => $model,
        ]);
    }

    public function actionCharge($id, $cur = 'balance', $mod = 'plus') {
        $model = $this->findModel($id);
        if($this->request->isPost){
            $amount = floatval(Yii::$app->request->post('amount'));
            if($cur != 'balance'){
                $amount = intval($amount);
            }
            if($amount < 0){
                $mod = $mod == 'plus' ? 'minus' : 'plus';
                $amount = -$amount;
            }
            if($amount == 0){
                return $this->redirect(['/cp/user/charge', 'id' => $id, 'cur' => $cur, 'mod' => $mod]);
            }
            $balance = new Balance();
            $balance->status = Balance::STATUS_ACTIVE;
            $balance->manual = 1;
            if($cur == 'balance'){
                if($mod == 'plus'){
                    $balance->type = Balance::TYPE_REFILL;
                    $balance->to_user_id = $model->id;
                    $balance->to_amount = $amount;
                }else{
                    $balance->type = Balance::TYPE_PAYOUT;
                    $balance->from_user_id = $model->id;
                    $balance->from_amount = $amount;
                }
            }else{
                if($mod == 'plus'){
                    $balance->type = Balance::TYPE_REFILL;
                    $balance->to_user_id = $model->id;
                    $balance->to_sapphire = $amount;
                }else{
                    $balance->type = Balance::TYPE_PAYOUT;
                    $balance->from_user_id = $model->id;
                    $balance->from_sapphire = $amount;
                }
            }
            $balance->save();
            return $this->redirect(['/cp/user/index']);
        }
        return $this->render('charge', [
            'model' => $model,
            'cur'   => $cur,
            'mod'   => $mod,
        ]);
    }

    public function actionLogin($id) {
        if(Yii::$app->user->id <> $id){
            $model = $this->findModel($id);
            $admin = Yii::$app->user->id;
            Yii::$app->user->logout(true);
            Yii::$app->user->login($model, 3600 * 24 * 30);
            Yii::$app->session->set('back-to-admin', $admin);
        }
        return $this->redirect(['/pm/default/index']);
    }

    public function actionBuyclone($id) {
        $model = $this->findModel($id);
        if($this->request->isPost && $table = intval(Yii::$app->request->post('table'))){
            $error = false;
            if($table < 1 || $table > 6){
                Yii::$app->session->addFlash('error', Yii::t('admin', 'Invalid table'));
                $error = true;
            }
            $activationAmount = Yii::$app->settings->get('system', 'promotionAmount' . $table);
            if(!$error && $model->balance - $model->accumulation < $activationAmount){
                Yii::$app->session->addFlash('error', Yii::t('admin', 'Insufficient balance'));
                $error = true;
            }
            if(!$error){
                $clones = $model->getClones();
                $clone = count($clones) + 1;
                $balance = new Balance();
                $balance->setAttributes([
                    'type'         => Balance::TYPE_ACTIVATION,
                    'status'       => Balance::STATUS_ACTIVE,
                    'from_amount'  => $activationAmount,
                    'table'        => 1,
                    'from_user_id' => $model->id,
                ]);
                $balance->save();
                $activation = new Activation();
                $activation->setAttributes([
                    'user_id' => $model->id,
                    'table'   => $table,
                    'start'   => $table,
                    'status'  => 1,
                    'clone'   => $clone,
                ]);
                $activation->buy = 1;
                if(!$activation->save()){
                    var_dump($activation->getErrors());
                    exit;
                };
                Yii::$app->session->addFlash('success', Yii::t('account', 'Successfully activated'));
            }
            return $this->redirect(['/cp/user/index']);
        }
        return $this->render('buyclone', [
            'model' => $model,
        ]);
    }


    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id ID
     *
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if(($model = User::findOne($id)) !== null){
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('admin', 'The requested page does not exist.'));
    }
}
