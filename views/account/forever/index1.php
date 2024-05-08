<?php

use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = Yii::t('account', 'Network Forever');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content">
    <div class="card">
        <div class="card-body">
            <a href="<?=Url::to(['/pm/activation-forever/index'])?>"><?=Yii::t('account', 'Activation required')?></a>
        </div>
    </div>
</div>
