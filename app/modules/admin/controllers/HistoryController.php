<?php

namespace app\modules\admin\controllers;

use app\models\search\HistorySearch;

/**
 * HistoryController implements the CRUD actions for History model.
 */
class HistoryController extends Controller
{

    /**
     * Lists all Activation models.
     *
     * @return string
     */
    public function actionIndex() {
        $searchModel = new HistorySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}
