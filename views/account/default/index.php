<?php

use yii\helpers\Url;
use yii\web\View;
use rmrevin\yii\fontawesome\FAS;
use app\models\Balance;

/* @var $this yii\web\View */
$this->title = Yii::t('account', 'Dashboard');
/**
 * @var $user \app\models\User
 */
$user = Yii::$app->user->identity;
?>
<?php /*if(!$user->isActive()): ?>
    <div class="card">
        <div class="card-body">
            <a href="<?=Url::to(['/pm/activation/index'])?>"><?=Yii::t('account', 'Activation required')?></a>
        </div>
    </div>
    <?php return ?>
<?php endif*/ ?>
<div class="row w-100">
    <div class="col-xs-12 col-sm-6 col-xl-3 p-3">
        <div class="inform_tour w-inline-block w-100">
            <div class="balance"><?=Yii::t('account', 'Balance')?></div>
            <div class="balance_amount">
                <span class="span text-nowrap"><?=number_format($user->balance - $user->accumulation, 2, '.', '')?> <?=FAS::icon('dollar-sign')?></span>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-xl-3 p-3">
        <div class="inform_tour w-inline-block w-100">
            <div class="balance"><?=Yii::t('account', 'Status')?></div>
            <div class="balance_amount">
                <span class="span text-nowrap"><?=$user->getActiveActivations()->orderBy(['table' => SORT_DESC])->select('table')->scalar()?> <?=Yii::t('account', 'Level')?></span>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-xl-3 p-3">
        <div class="inform_tour w-inline-block w-100">
            <div class="balance text-nowrap"><?=Yii::t('account', 'Sapphire tour')?></div>
            <div class="balance_amount pr-5">
                <span class="span text-nowrap"><?=$user->sapphire?> <?=Yii::t('account', 'Points')?> <?=Yii::t('account', 'of')?> 36</span>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-xl-3 p-3">
        <div class="inform_tour w-inline-block w-100">
            <div class="balance"><?=Yii::t('account', 'Earned')?></div>
            <div class="balance_amount">
                <span class="span text-nowrap"><?=sprintf('%0.2f', Balance::find()->where(['to_user_id' => $user->id, 'type' => Balance::TYPE_CHARGING])->select('SUM(`to_amount`)')->scalar())?> <?=FAS::icon('dollar-sign')?></span>
            </div>
        </div>
    </div>
</div>