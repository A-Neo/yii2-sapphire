<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\User;
use rmrevin\yii\fontawesome\FAS;
use app\components\Api;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('admin', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index table">
    <p>
        <?=Html::a(Yii::t('admin', 'Create User'), ['create'], ['class' => 'btn btn-success'])?>
    </p>
    <?php Pjax::begin(); ?>
    <?=GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => [
            ['class' => 'yii\grid\SerialColumn'],
            'username',
            'full_name',
            'email:email',
            [
                'attribute' => 'referrer',
                'value'     => 'referrer.username',
            ],
            [
                'attribute' => 'balance',
                'format'    => 'raw',
                'value'     => function (User $model) {
                    return Api::asNumber($model->balance) . ($model->balance - $model->accumulation > 0 ? ' ' . Html::a(FAS::i('exchange-alt text-primary'), ['/cp/user/exchange', 'id' => $model->id], [
                                'title' => Yii::t('admin', 'Transfer money to another user'),
                            ]) : '')
                        . ' ' . Html::a(FAS::i('hand-holding-usd text-success'), ['/cp/user/charge', 'id' => $model->id, 'cur' => 'balance', 'mod' => 'plus'], [
                            'title' => Yii::t('admin', 'Charge to user\'s balance'),
                        ])
                        . ' ' . Html::a(FAS::i('money-bill-wave  text-danger'), ['/cp/user/charge', 'id' => $model->id, 'cur' => 'balance', 'mod' => 'minus'], [
                            'title' => Yii::t('admin', 'Withdraw from user\'s balance'),
                        ])
                        . '<br/><span class="text-gray">' . Api::asNumber($model->accumulation) . '</span>';
                },
            ],

            [
                'attribute' => 'sapphire',
                'format'    => 'raw',
                'value'     => function (User $model) {
                    return $model->sapphire
                        . ' ' . Html::a(FAS::i('hand-holding-usd text-success'), ['/cp/user/charge', 'id' => $model->id, 'cur' => 'sapphire', 'mod' => 'plus'], [
                            'title' => Yii::t('admin', 'Charge to user\'s sapphire'),
                        ])
                        . ' ' . Html::a(FAS::i('money-bill-wave  text-danger'), ['/cp/user/charge', 'id' => $model->id, 'cur' => 'sapphire', 'mod' => 'minus'], [
                            'title' => Yii::t('admin', 'Withdraw from user\'s sapphire'),
                        ])
                        . '<br/><span class="text-gray">' . $model->sapphire_personal . '/' . $model->sapphire_partners . '</span>';
                },
            ],
            [
                'attribute' => 'partnersCount',
                'label'     => Yii::t('site', '1-st Line'),
            ],
            /*'structuresCount',*/
            [
                'class'           => 'app\components\columns\EditableColumn',
                'attribute'       => 'role',
                'type'            => 'select',
                'value'           => function ($model) {
                    return $model->roleName;
                },
                'editableOptions' => function ($model) {
                    return [
                        'source' => $model->getRolesList(),
                        'mode'   => 'inline',
                    ];
                },
            ],
            [
                'class'           => 'app\components\columns\EditableColumn',
                'attribute'       => 'permissions',
                'type'            => 'checklist',
                'value'           => function ($model, $key, $index, $column) {
                    if(is_string($model->permissions)){
                        try{
                            $model->permissions = json_decode($model->permissions, true);
                            if(empty($model->permissions)){
                                $model->permissions = [];
                            }
                        }catch(\Exception $e){
                            $model->permissions = [];
                        }
                    }
                    return $model->role == User::ROLE_ADMIN ? Yii::t('admin', 'All') :
                        ($model->role == User::ROLE_MODERATOR ? $model->permissionsNames : null);
                },
                'editableOptions' => function ($model) {
                    return [
                        'source'   => $model->getPermissionsList(),
                        'mode'     => 'inline',
                        'disabled' => $model->role != User::ROLE_MODERATOR,
                    ];
                },
            ],
            [
                'class'           => 'app\components\columns\EditableColumn',
                'attribute'       => 'status',
                'type'            => 'select',
                'value'           => function ($model) {
                    return $model->statusName;
                },
                'editableOptions' => function ($model) {
                    return [
                        'source' => $model->getStatusesList(),
                        'mode'   => 'inline',
                    ];
                },
            ],
            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{update} {buyclone} {login} ',
                'buttons'  => [
                    'login'    => function ($url, $model, $key) {
                        return Html::a(FAS::i('sign-in-alt'), $url, ['title' => Yii::t('admin', 'Login as user')]);
                    },
                    'buyclone' => function ($url, $model, $key) {
                        return Html::a(FAS::i('users text-info'), $url, ['title' => Yii::t('admin', 'Buy clone')]);
                    },
                ],
            ],
        ],
    ]);?>
    <?php Pjax::end(); ?>
</div>
