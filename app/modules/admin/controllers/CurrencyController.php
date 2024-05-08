<?php

namespace app\modules\admin\controllers;

use app\models\News;
use app\models\search\NewsSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii2mod\toggle\actions\ToggleAction;
use app\models\Currency;
use yii\data\ActiveDataProvider;

/**
 * NewsController implements the CRUD actions for News model.
 */
class CurrencyController extends Controller
{

    public function actions() {
        return [
            'toggle' => [
                'class'      => ToggleAction::class,
                'modelClass' => Currency::class,
            ],
        ];
    }

    /**
     * Lists all Currency models.
     *
     * @return mixed
     */
    public function actionIndex() {
        $query = Currency::find();
        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => [
                'defaultOrder' => ['id' => SORT_ASC],
            ],
            'pagination' => [
                'defaultPageSize' => 50,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Currency model.
     * If creation is successful, the browser will be redirected to the 'view' News.
     *
     * @return mixed
     */
    public function actionCreate() {
        $model = new Currency();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Currency model.
     * If update is successful, the browser will be redirected to the 'view' News.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing News model.
     * If deletion is successful, the browser will be redirected to the 'index' News.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return News the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Currency::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('site', 'The requested news does not exist.'));
    }

}
