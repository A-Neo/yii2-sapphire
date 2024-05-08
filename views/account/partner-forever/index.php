<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\View;
use app\models\User;
use rmrevin\yii\fontawesome\FAS;
use yii\bootstrap4\LinkPager;

/* @var $this yii\web\View */
/* @var $id int */
$id = !empty($id) ? $id : 1;
$this->title = Yii::t('account', 'Personally invited');
/**
 * @var $user         \app\models\User
 * @var $dataProvider \yii\data\ArrayDataProvider
 */
$c = 1;
if(!empty($dataProvider)){
    $users = $dataProvider->getModels();
}else{
    $users = $user->referrals;
}
$identity = Yii::$app->user->identity;
$this->registerJs(<<<JS
    $('.fas.text-success').on('click', function(e){
       e.preventDefault();
       $(this).next().toggleClass('d-none');
       return false;
    });
JS
);
?>
<div class="w-100 wrapper_form-balance">
    <?php if($user->id <> $identity->id): ?>
        <p class="text-left">
            <?=Yii::t('account', 'Personally invited by user')?>:
            <b><?=$user->username?></b>

        </p>

        <p class="text-left"><a href="<?=Url::to(['/pm/partner/index', 'ref' => $user->referrer_id <> $identity->id ? $user->referrer_id : null])?>">&lt;&lt;&lt; <?=Yii::t('account', 'Back')?></a></p>
    <?php endif ?>
    <p><a href="/pm/partner">Sapphire</a></p>
    <p class="text-left">
    <form method="get">
        <div class="row">
            <div class="col-2">
                <input type="text" class="form-control" name="search" value="<?=$search?>" placeholder="<?=Yii::t('site', 'Username')?>"/>
            </div>
            <div class="col-1">
                <button class="btn btn-success"><?=Yii::t('account', 'Search')?></button>
            </div>
        </div>
    </form>
    </p>
    <table class="table table-striped">
        <tr>
            <th>№</th>
            <th><?=Yii::t('site', 'Username')?></th>
            <th><?=Yii::t('site', 'Status')?></th>
            <?php for($n = 1; $n <= 4; $n++): ?>
                <th><?=Yii::t('account', 'Table {n}', ['n' => $n])?></th>
            <?php endfor ?>
            <th><?=Yii::t('site', '1-st Line')?></th>
            <th><?=Yii::t('site', 'Phone')?></th>
        </tr>
        <?php foreach($users as $referral): ?>
            <?php if($referral->id == $user->id) continue; ?>
            <?php $activations = $referral->activeActivationsForever; ?>
            <tr>
                <td><?=!empty($dataProvider) ? $dataProvider->pagination->page * $dataProvider->pagination->pageSize + ($c++) : $c++?></td>
                <td>
                    <?php if($referral->status == User::STATUS_ACTIVE): ?>
                        <a href="<?=Url::to(['/pm/partner/index', 'ref' => $referral->id])?>"><?=$referral->username?></a>
                    <?php else: ?>
                        <?=$referral->username?>
                    <?php endif ?>
                </td>
                <td><?=FAS::icon($referral->status == User::STATUS_ACTIVE ? 'check text-success' : 'times text-danger')?></td>
                <?php for($n = 1; $n <= 4; $n++): ?>
                    <th>
                        <?php if($referral->status == User::STATUS_ACTIVE): ?>
                            <?php $not = true; ?>
                            <?php foreach($activations as $activation){
                                if($activation->table < $n || $activation->start > $n){
                                    continue;
                                }
                                $u = $activation->isUsed($n);
                                $t = '';
                                if(!$u){
                                    $d = 0;
                                    $h = 24 * 3 - floor((time() - $activation->updated_at) / 3600);
                                    if(abs($h) > 24){
                                        $d = floor($h / 24);
                                        $h -= $d * 24;
                                    }
                                    $t = '<span style="font-weight: normal">(' . ($d ? $d . Yii::t('account', 'd.') : '') . ($h ? $h . Yii::t('account', 'h.') : '') . ')</span>';
                                }
                                echo FAS::icon($u ? 'check text-success' : 'exclamation text-danger', [
                                        'data-id' => $activation->id,
                                        'title'   => $title = ($top = $activation->getTop($n)) ? $top->user->username . ($top->clone ? '(K ' . $top->clone . ')' : '') : '',
                                        'style'   => $title ? 'cursor:pointer' : '',
                                    ]) . $t . ' ';
                                echo $title ? Html::tag('div', $title, ['class' => 'd-none']) : '';
                                $not = false;
                            } ?>
                            <?php if($not): ?>
                                <?php if($n == 1 && $identity->balance - $identity->accumulation >= Yii::$app->settings->get('system', 'activationForeverAmount')): ?>
                                    <a href="/pm/activation-forever/<?=$referral->id?>" class="btn btn-success"><?=Yii::t('account', 'Activate from own balance')?></a>
                                <?php else: ?>
                                    <?=FAS::icon('times text-warning')?>
                                <?php endif ?>
                            <?php endif ?>
                        <?php endif ?>
                    </th>
                <?php endfor ?>
                <td><?=$referral->partnersCount?></td>
                <td><?=$referral->phone?></td>
            </tr>
        <?php endforeach ?>
    </table>
    <?php if(!empty($dataProvider)): ?>
        <?=LinkPager::widget([
            'pagination' => $dataProvider->pagination,
        ])?>
    <?php endif ?>
</div>