<?php

use rmrevin\yii\fontawesome\FAS;

/* @var $this \yii\web\View */

$this->title = Yii::t('site', 'Under construction');
?>
<div class="card col-12 alert-warning">
    <div class="card-title">
        <br/>
        <h3><?= FAS::i('tools') ?> <?= $this->title ?></h3>
    </div>
    <div class="card-body">
        <h4><?= Yii::t('site', 'The advanced feature you have requested is under construction.') ?></h4>
    </div>
</div>
