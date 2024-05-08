<?php

namespace app\modules\account\controllers;

use app\models\Shop;
class ShopController extends Controller
{

    public function actionIndex() {
        $product = ['id' => 1, 'name' => 'Пластырь'];
        return $this->render('index', array('product' => $product));
    }

}
