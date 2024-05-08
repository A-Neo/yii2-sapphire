<?php

namespace app\modules\admin\controllers;

use app\models\Balance;
use app\models\enumerables\SettingType;
use app\models\search\BalanceSearch;
use Yii;

/**
 * BalanceController implements the CRUD actions for Balance model.
 */
class BalanceController extends Controller
{

    /**
     * Lists all Balance models.
     *
     * @return string
     */
    public function actionIndex() {
        $searchModel = new BalanceSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionApprove($id = null) {
        if(empty($id)){
            return $this->redirect(['/cp/balance']);
        }
        $model = Balance::findOne($id);
        if(empty($model)){
            return $this->redirect(['/cp/balance']);
        }
        if($model->status != Balance::STATUS_WAITING){
            return $this->redirect(['/cp/balance']);
        }
        $model->status = Balance::STATUS_ACTIVE;
        $model->save();
        return $this->redirect(['/cp/balance']);
    }

    public function actionReject($id = null) {
        if(empty($id)){
            return $this->redirect(['/cp/balance']);
        }
        $model = Balance::findOne($id);
        if(empty($model)){
            return $this->redirect(['/cp/balance']);
        }
        if($model->status != Balance::STATUS_WAITING){
            return $this->redirect(['/cp/balance']);
        }
        $model->status = Balance::STATUS_INACTIVE;
        $model->save();
        return $this->redirect(['/cp/balance']);
    }

    public function actionReturn($id = null) {
        if(empty($id)){
            return $this->redirect(['/cp/balance']);
        }
        $model = Balance::findOne($id);
        if(empty($model)){
            return $this->redirect(['/cp/balance']);
        }
        if($model->type != Balance::TYPE_TRANSFER || empty($model->fromUser) || empty($model->toUser) || ($model->toUser->balance - $model->toUser->accumulation < $model->to_amount)){
            return $this->redirect(['/cp/balance']);
        }
        $fee = Yii::$app->settings->get('system', 'transferFee');
        $fee = ceil($model->to_amount * $fee) / 100;
        $value = Yii::$app->settings->get('system', 'transferFeeValue', 0);
        $value += $fee;
        Yii::$app->settings->set('system', 'transferFeeValue', $value, SettingType::FLOAT_TYPE, 2);
        $new = new Balance();
        $new->setAttributes([
            'type'         => Balance::TYPE_TRANSFER,
            'status'       => Balance::STATUS_ACTIVE,
            'from_user_id' => $model->to_user_id,
            'to_user_id'   => $model->from_user_id,
            'from_amount'  => $model->to_amount,
            'to_amount'    => $model->to_amount - $fee,
        ]);
        $new->save();
        return $this->redirect(['/cp/balance']);
    }

}
