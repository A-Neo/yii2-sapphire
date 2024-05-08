<?php

use yii\helpers\Url;
use app\models\Tour;
use app\models\TourName;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use kartik\widgets\FileInput;

/* @var $this yii\web\View */
$this->title = Yii::t('account', 'Sapphire tour');
/**
 * @var $user  \app\models\User
 * @var $model \app\models\Tour
 */
$user = Yii::$app->user->identity;
$allTours = TourName::findAll(['status' => TourName::STATUS_ACTIVE]);
$availableTours = [];
foreach($allTours as $allTour){
    if($user->sapphire_personal >= 3 && ($user->sapphire_partners >= $allTour->price - 3)){
        $availableTours[] = $allTour;
    }
}
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
        <div class="col-xs-12 col-sm-6 col-lg-4 col-xl-3 p-3">
            <div class="inform_tour w-inline-block w-100">
                <div class="balance text-nowrap"><?=Yii::t('account', 'Balance')?></div>
                <div class="balance_amount pr-5">
                    <span class="span text-nowrap"><?=$user->sapphire?> <?=Yii::t('account', 'Points')?></span><br/>
                    <span class="span text-nowrap font-weight-normal"><?=$user->sapphire_personal?> <?=Yii::t('account', 'Personal')?></span><br/>
                    <span class="span text-nowrap font-weight-normal"><?=$user->sapphire_partners?> <?=Yii::t('account', 'From 1st line')?></span>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-lg-8 col-xl-9">
            <div class="inform_tour w-inline-block w-100">
                <div class="balance"><?=Yii::t('account', 'Tour cost')?></div>
                <div class="balance_amount">
                    <div class="row w-100">
                        <?php foreach($allTours as $allTour): ?>
                            <div class="col-12 col-lg-6 col-xl-4">
                                <span class="span text-nowrap"><?=$allTour->t_name?></span><br/>
                                <span class="span text-nowrap"><?=$allTour->price?> <?=Yii::t('account', 'Points')?></span><br/>
                                <span class="span text-nowrap font-weight-normal">3 <?=Yii::t('account', 'Personal')?></span><br/>
                                <span class="span text-nowrap font-weight-normal"><?=$allTour->price - 3?> <?=Yii::t('account', 'From 1st line')?></span>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php if(empty($user->tours) || $availableTours || !$user->isActive()): ?>
    <div class="row w-100">
        <div class="col-xs-12 col-sm-6">
            <div class="inform_tour w-inline-block w-100">
                <div class="balance_amount pr-5 ">
                    <?php if($availableTours || !$user->isActive()): ?>
                        <span class="span"><?=Yii::t('account', 'You have not yet met the conditions for participation in the program')?></span>
                    <?php else: ?>
                        <span class="span"><?=Yii::t('account', 'Please fill in the form')?></span>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
<?php endif ?>
<?php if($availableTours): ?>
    <?=$this->render('_form', ['model' => $model, 'availableTours' => $availableTours])?>
<?php endif ?>
<?php foreach($user->getTours()->orderBy(['id' => SORT_DESC])->all() as $tour): ?>
    <?=$this->render('_form', ['model' => $tour, 'availableTours' => $availableTours])?>
<?php endforeach ?>