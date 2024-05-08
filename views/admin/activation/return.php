<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $activation app\models\Activation */
/* @var $balance app\models\Balance */
/* @var $message string */

$this->title = $activation->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('site', 'Activations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$t = $activation->table;
?>
<h3><?=$message?></h3>
<div class="row">
    <div class="activation-view col-xs-12 col-md-6">
        <h4><?=Yii::t('admin', 'Activation')?></h4>
        <?=DetailView::widget([
            'model'      => $activation,
            'attributes' => [
                'id',
                'top.top.user.username',
                'top.user.username',
                'user.username',
                'table',
                'clone',
                "t{$t}_left",
                "t{$t}_right",
                'created_at:dateTime',
                'updated_at:dateTime',
            ],
        ])?>
    </div>
    <div class="activation-view col-xs-12 col-md-6">
        <h4><?=Yii::t('admin', 'Accrual')?></h4>
        <?=$balance ? DetailView::widget([
            'model'      => $balance,
            'attributes' => [
                'id',
                'fromUser.username',
                'toUser.username',
                'to_amount',
                'created_at:dateTime',
            ],
        ]) : '-=//=-<br/>'?>
        <?=Html::a(Yii::t('admin', 'Return into stop list'), Url::to(['/cp/activation/return', 'id' => $activation->id, 'confirm' => true]), [
            'class'        => 'btn btn-danger',
            'title'        => Yii::t('admin', 'Return into stop list'),
            'data-confirm' => Yii::t('admin', 'Are you sure you want to return this item to stop list?'),
        ])?>
    </div>
</div>
