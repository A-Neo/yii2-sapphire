<?php

namespace app\controllers;

use app\models\Page;
use Yii;
use yii\web\NotFoundHttpException;

class PageController extends \yii\web\Controller
{

    public function actionShow($slug, $category = null) {
        $view = 'show';
        if (file_exists($this->getViewPath() . '/' . $slug . '.php')) {
            $view = $slug;
        }
        $page = Page::findOne(['slug' => $slug, 'status' => 1]);
        if (empty($page) && $view == 'show') {
            throw new NotFoundHttpException(Yii::t('site', 'Page not found'));
        }
        $search = Yii::$app->request->get('search', Yii::$app->request->post('search'));
        return $this->render($view, ['page' => $page, 'category' => $category, 'search' => $search]);
    }

}
