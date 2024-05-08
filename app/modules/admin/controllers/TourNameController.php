<?php

namespace app\modules\admin\controllers;

use app\models\TourName;
use app\models\search\TourNameSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use yii2mod\toggle\actions\ToggleAction;

/**
 * TourNameController implements the CRUD actions for TourName model.
 */
class TourNameController extends Controller
{
    public function actions() {
        return [
            'toggle' => [
                'class'      => ToggleAction::class,
                'modelClass' => TourName::class,
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function behaviors() {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class'   => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all TourName models.
     *
     * @return string
     */
    public function actionIndex() {
        $searchModel = new TourNameSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new TourName model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate() {
        $model = new TourName();
        if($this->request->isPost){
            if($model->load($this->request->post()) && $model->save()){
                return $this->redirect(['index']);
            }
        }else{
            $model->status = 1;
            $model->price = 3;
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TourName model.
     * If update is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id ID
     *
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if($this->request->isPost && $model->load($this->request->post()) && $model->save()){
            return $this->redirect(['index', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TourName model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id ID
     *
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        if(!$this->findModel($id)->delete()){
            Yii::$app->session->addFlash('error', Yii::t('admin', 'You can\'t delete tour with filled applications'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the TourName model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id ID
     *
     * @return TourName the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if(($model = TourName::findOne(['id' => $id])) !== null){
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('admin', 'The requested page does not exist.'));
    }
}
