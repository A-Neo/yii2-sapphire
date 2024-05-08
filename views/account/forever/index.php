<?php

use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = Yii::t('account', 'My network');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content">
    <div class="card">
        <div class="card-body">
            <a href="<?=Url::to(['/pm/activation/index'])?>"><?=Yii::t('account', 'Activation required')?></a>
        </div>
    </div>
</div>
