<?php

namespace app\modules\account\controllers;

use app\models\News;
use app\models\search\NewsSearch;
use Yii;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use Zelenin\yii\extensions\Rss\RssView;

class NewsController extends Controller
{

    public function actionIndex() {
        $searchModel = new NewsSearch();
        $searchModel->pageSize = 10;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id) {
        $news = News::findOne(['id' => $id, 'status' => 1]);
        if (empty($news)) {
            throw new NotFoundHttpException(Yii::t('site', 'News not found'));
        }
        return $this->render('view', ['news' => $news]);
    }

}
