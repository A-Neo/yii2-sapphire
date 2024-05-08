<?php

namespace app\modules\admin\controllers;

use app\models\Page;
use app\models\search\PageSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii2mod\toggle\actions\ToggleAction;
use yii2tech\admin\actions\Position;


/**
 * PageController implements the CRUD actions for Page model.
 */
class PageController extends Controller
{

    public function actions() {
        return [
            'toggle'   => [
                'class'      => ToggleAction::class,
                'modelClass' => Page::class,
            ],
            'position' => [
                'class' => Position::class,
            ],
        ];
    }

    /**
     * Lists all Page models.
     *
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new PageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $templates = glob(str_replace('/cp/', '/', $this->getViewPath()) . '/*.php');
        /*$slugs = [];
        foreach ($templates as $template) {
            $slugs [] = pathinfo($template, PATHINFO_FILENAME);
        }
        $slugs = array_diff($slugs, ['show']);
        if ($exists = Page::find()->where(['slug' => $slugs])->select('slug')->column()) {
            $slugs = array_diff($slugs, $exists);
        }*/

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            //'slugs'        => $slugs,
        ]);
    }

    /**
     * Creates a new Page model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate($slug = null) {
        $model = new Page();
        $model->slug = $slug ?? null;
        if($model->load(Yii::$app->request->post()) && $model->save()){
            return $this->redirect(['index']);
        }else{
            $model->status = 1;
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Page model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if($model->load(Yii::$app->request->post()) && $model->save()){
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Page model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Page model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Page the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModel($id) {
        if(($model = Page::findOne($id)) !== null){
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('site', 'The requested page does not exist.'));
    }

}
