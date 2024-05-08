<?php

namespace app\modules\admin\controllers;

use app\models\Activation;
use app\models\Payout;
use app\models\TravelMain;
use app\models\User;
use app\components\Api;
use app\components\PmApi;
use Yii;
use yii\caching\TagDependency;

class DefaultController extends Controller
{

    public function actionIndex() {
        $today = strtotime(date('d-m-Y'));
        $yesterday = $today - 24 * 3600;
        $week = $today - 24 * 3600 * 7;
        $month = $today - 24 * 3600 * 30;
        $info = [
            [
                'label' => Yii::t('admin', 'Balance'),
                'value' => '<b>Payeer</b>: '.Api::balance().'<br/>'.
                    '<b>PerfectMoney</b>: '.PmApi::balance().'<br/>',
            ],
            [
                'label' => Yii::t('admin', 'Users'),
                'value' => Yii::t('admin', 'Count') . ': ' . User::find()->cache(60, new TagDependency(['tags' => 'users']))->count() . '<br/>' .
                    Yii::t('admin', 'Balance') . ': ' . Api::asNumber(User::find()->select('SUM(`balance`)')->cache(60, new TagDependency(['tags' => 'users']))->scalar()),
            ],
            [
                'label' => Yii::t('admin', 'Payout requests'),
                'value' => Yii::t('admin', 'Count') . ': ' . Payout::find()->cache(60, new TagDependency(['tags' => 'payouts']))->where(['status' => Payout::STATUS_INACTIVE])->count() . '<br/>' .
                    Yii::t('admin', 'Sum') . ': ' . Api::asNumber(Payout::find()->select('SUM(`amount`)')->cache(60, new TagDependency(['tags' => 'payouts']))->where(['status' => Payout::STATUS_INACTIVE])->scalar()),
            ],
            [
                'label' => Yii::t('admin', 'Fee'),
                'value' => Yii::t('admin', 'For payouts') . ': ' . Api::asNumber(Yii::$app->settings->get('system', 'payoutFeeValue', 0)) . '<br/>'
            ],
            [
                'label' => Yii::t('admin', 'Activations Saphire'),
                'value' => Yii::t('admin', 'Today') . ': ' . Activation::find()->where(['clone' => 0, 'status' => Activation::STATUS_ACTIVE])->andWhere(['>=', 'created_at', $today])->cache(60, new TagDependency(['tags' => 'activations']))->count() . '<br/>' .
                    Yii::t('admin', 'All time') . ': ' . Activation::find()->where(['clone' => 0, 'status' => Activation::STATUS_ACTIVE])->cache(60, new TagDependency(['tags' => 'activations']))->count() . '<br/>',
            ],
            [
                'label' => Yii::t('admin', 'By countries'),
                'value' => User::byCountries(),
            ],
            [
                'label' => Yii::t('admin', 'Activations Travel'),
                'value' => Yii::t('admin', 'Today') . ': ' . TravelMain::find()->where(['level' => 1])->andWhere(['>=', 'created_at', $today])->cache(60, new TagDependency(['tags' => 'activations']))->count() . '<br/>' .
                    Yii::t('admin', 'All time') . ': ' . TravelMain::find()->where(['level' => 1])->cache(60, new TagDependency(['tags' => 'activations']))->count() . '<br/>',
            ],
        ];
        return $this->render('index', [
            'info' => $info,
        ]);
    }

}
