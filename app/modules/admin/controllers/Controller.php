<?php

namespace app\modules\admin\controllers;

use yii\filters\AccessControl;
use Yii;

class Controller extends \yii\web\Controller
{

    public function behaviors() {
        return [
            'access' => [
                'class'        => AccessControl::class,
                'denyCallback' => function ($user) {
                    if(Yii::$app->user->isGuest){
                        $this->response->redirect(['/site/login', 'ret' => $this->request->url]);
                    }else if(Yii::$app->controller->id != 'default'){
                        $this->response->redirect(['/cp']);
                    }else{
                        $this->response->redirect(['/']);
                    }
                },
                'rules'        => [
                    [
                        'actions'     => ['index'],
                        'controllers' => ['cp/default'],
                        'allow'       => true,
                        'roles'       => ['admin', 'moderator'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['admin', explode('-', $this->id)[0]],
                    ],
                ],
            ],
        ];
    }

}