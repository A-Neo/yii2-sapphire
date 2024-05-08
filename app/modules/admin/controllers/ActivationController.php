<?php

namespace app\modules\admin\controllers;

use app\models\Balance;
use Yii;
use app\models\Activation;
use app\models\search\ActivationSearch;

/**
 * ActivationController implements the CRUD actions for Activation model.
 */
class ActivationController extends Controller
{

    /**
     * Lists all Activation models.
     *
     * @return string
     */
    public function actionIndex() {
        $searchModel = new ActivationSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionReturn($id, $confirm = 0) {
        $model = Activation::findOne($id);
        $t = $model->table;
        $message = '';
        if(!empty($model->{"t{$t}_left"}) || !empty($model->{"t{$t}_right"})){
            $message = Yii::t('admin', 'You cannot return to the stop list the place under which someone is already installed');
        }
        $top = $model->getTop($t);
        if(empty($top) || $top->table > $t || $top->status == Activation::STATUS_CLOSED){
            $message = Yii::t('admin', 'You cannot return to the stop list, if the superior location has already moved to the next matrix or is closed');
        }
        $topTop = $top->getTop($t);
        if(empty($topTop) || $topTop->table > $t || $topTop->status == Activation::STATUS_CLOSED){
            $message = Yii::t('admin', 'You cannot return to the stop list, if the superior location has already moved to the next matrix or is closed') . '*';
        }
        if($topTop && $topTop->status != Activation::STATUS_CLOSED && $topTop->table == $t + 1 && !$topTop->isUsed($t + 1)){
            $message = '';
        }
        $balance = Balance::find()->where([
            'from_activation_id' => $model->id,
            'to_activation_id'   => $topTop->id,
            'to_amount'          => Yii::$app->settings->get('system', 'chargingAmount' . $model->table),
        ])->one();
        if($confirm && empty($message)){
            if($balance){
                $balance->delete();
            }
            if($topTop->table == $t + 1 && !$topTop->isUsed($t + 1)){
                $balance = Balance::find()->where([
                    'from_amount' => Yii::$app->settings->get('system', 'promotionAmount' . $topTop->table),
                ])->andWhere(['OR', [
                    'from_activation_id' => $topTop->id,
                    'from_user_id'       => $topTop->user_id,
                ], [
                        'from_activation_id' => null,
                        'from_user_id'       => $topTop->user_id,
                    ]
                ])->one();
                if($balance){
                    $balance->delete();
                }
                $topTop->table-=1;
                $topTop->touch('updated_at');
                $topTop->save();
            }
            if($top){
                Yii::$app->session->set('disableAutoSet', true);
                $top->{"t{$t}_left"} = null;
                $top->{"t{$t}_right"} = null;
                $top->save();
                $model->touch('updated_at');
                $model->save();
            }
            return $this->redirect(['/cp/activation']);
        }
        return $this->render('return', [
            'activation' => $model,
            'balance'    => $balance,
            'message'    => $message,
        ]);
    }

}
