<?php

namespace app\modules\account\controllers;

use yii\filters\AccessControl;
use Yii;

class Controller extends \yii\web\Controller
{
    public function behaviors() {
        return [
            'access' => [
                'class'        => AccessControl::class,
                'denyCallback' => function ($user) {
                    if (Yii::$app->user->isGuest) {
                        $this->response->redirect(['/site/login', 'ret' => $this->request->url]);
                    } else {
                        $this->response->redirect(['/']);
                    }
                },
                'rules'        => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

}
