<?php

namespace app\modules\admin\controllers;

use app\models\Balance;
use app\models\enumerables\SettingType;
use app\models\Payout;
use app\models\search\PayoutSearch;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * PayoutController implements the CRUD actions for Payout model.
 */
class PayoutController extends Controller
{
    /**
     * Lists all Payout models.
     *
     * @return string
     */
    public function actionIndex() {
        $searchModel = new PayoutSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionReject($id) {
        $model = $this->findModel($id);
        if($model->status == Payout::STATUS_INACTIVE){
            $model->status = Payout::STATUS_REJECT;
            $model->save();
            $balance = $model->balance;
            $balance->status = Balance::STATUS_INACTIVE;
            $balance->save();
        }
        return $this->redirect(['/cp/payout/index']);
    }

    public function actionProcess($id) {
        $model = $this->findModel($id);
        if($model->status == Payout::STATUS_INACTIVE){
            $model->status = Payout::STATUS_ACTIVE;
            $value = Yii::$app->settings->get('system', 'payoutFeeValue', 0);
            $fee = Yii::$app->settings->get('system', 'payoutFee');
            $value += ceil($model->amount * $fee) / 100;
            Yii::$app->settings->set('system', 'payoutFeeValue', $value, SettingType::FLOAT_TYPE, 2);
            $model->save();
        }
        return $this->redirect(['/cp/payout/index']);
    }

    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Payout the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if(($model = Payout::findOne($id)) !== null){
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('site', 'The requested payout does not exist.'));
    }
}
