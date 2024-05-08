<?php

use yii\helpers\Url;
use yii\web\View;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $id int */
/* @var $search string */
/* @var $user \app\models\User */
$id = !empty($id) ? $id : 1; // Номер стола (урвоень)
$this->title = Yii::t('account', 'My network');

$users = [];
$uu = [];

$identity = Yii::$app->user->identity;

foreach($identity->referrals as $referral){
    if($referral->id == Yii::$app->user->identity->id) continue;
    foreach($referral->getActiveActivations()->orderBy(['clone' => SORT_ASC])->all() as $activation){
        if($activation->table >= $id && $activation->start <= $id && !$activation->isUsed($id)){
            $r = ['username' => $referral->username];
            $r['id'] = $activation->id;
            if($activation->clone){
                $r['username'] .= ' (' . Yii::t('account', 'C') . $activation->clone . ')';
            }
            $users[] = (object)$r;
        }
    }
}
foreach($identity->activeActivations as $activation){
    $activation->checkTx($activation);
    if($activation->table < $id || $activation->start > $id){
        continue;
    }
    if($activation->clone > 0 && !$activation->isUsed($id)){
        $r = ['username' => $identity->username];
        $r['id'] = $activation->id;
        $r['username'] .= ' (' . Yii::t('account', 'C') . $activation->clone . ')';
        $users[] = (object)$r;
    }
}
$images = [
    '62630c5e4a12d76c1bd396eb_stol1_n_rad.png',
    '6263195a043c834764b81462_stol2_n_rad.png',
    '62631aab007b3565f062e757_stol3_n_rad.png',
    '62631b8fe548ae7996a0462b_stol4_n_rad.png',
    '62631cbe3db7196565ccf8e6_stol5_n_rad.png',
    '62631d67d46283593377b9c6_stol6_n_rad.png',
];
$imagesV = [
    '62630c5e5b02498fd2c780d3_stol1_v_rad.png',
    '6263195acbbe4647b5624e0b_stol2_v_rad.png',
    '62631a9c75fa5a970d26251b_stol3_v_rad.png',
    '62631b84bc65e813d4d09a06_stol4_v_rad.png',
    '62631cbe4a12d71b6cd422cd_stol5_v_rad.png',
    '62631d6648abd17bf77eacbb_stol6_v_rad.png',
];

$imageV = AppAsset::image($imagesV[$id - 1]);
$image = AppAsset::image($images[$id - 1]);

$this->registerJs(<<<JS

function copyToClipboard(element) {
    var \$temp = $("<input>");
    $("body").append(\$temp);
    \$temp.val($(element).text()).select();
    document.execCommand("copy");
    \$temp.remove();
    alert("Ссылка скопирована");
}

JS
    , View::POS_END);
if(!empty($users)){
    $this->registerJs(<<<JS
    $('.select-button[data-at]').on('click', function(){
        $('#save-user').data('at', $(this).data('at'));
        $('#save-user').data('side', $(this).data('side'));
        $('#modal').modal('show');
    });
    $('#save-user').on('click', function(){
        var id =  $('#user-selection').val();
        var at =  $('#save-user').data('at');
        var side =  $('#save-user').data('side');
        if(id) {
            window.location.href ='/pm/network/set?id='+id+'&at='+at+'&t={$id}&side='+side+'&search={$search}';
        }
    });
JS
    );
}
?>
<div class="section_my-set wf-section">
    <?php
    $tables = [];
    for($t = 1; $t <= 6; $t++){
        $tables[] = [
            'label'    => Yii::t('account', 'Table {n}', ['n' => $t]),
            'url'      => ['/pm/network/index', 'id' => $t],
            'active'   => $id == $t,
            'disabled' => !$user->isActive($t),
        ];
    }
    ?>
    <?=\yii\bootstrap4\Tabs::widget([
        'options'       => [
            'tag'   => 'div',
            'class' => 'wrapp_block-btn-stol',
        ],
        'headerOptions' => [
            'tag' => '',
        ],
        'linkOptions'   => [
            'class' => [
                'widget'   => 'btn_stol w-button',
                'activate' => 'on',
            ],
        ],
        'items'         => $tables,
    ]);?>
</div>
<div class="section_line wf-section"></div>
<div class="section_search-sapfir wf-section mb-5">
    <div class="wrapp_search">
        <form method="get" action="<?=Url::to(['/pm/network/index', 'id' => $id])?>" class="search w-form">
            <input type="text" class="search-input w-input" name="search"/>
            <input type="submit" class="search-button w-button" value="<?=Yii::t('account', 'Search')?>"/>
        </form>
    </div>
    <div class="wrapp_block-btn-stol mt-3 pl-1vw" id="tab-clones">
        <?php
        $a = true;
        $c = 0;
        ?>
        <?php foreach($user->activeActivations as $activation): ?>
            <?php
            if($activation->table < $id || $activation->start > $id){
                continue;
            }
            ?>
            <a href="#tab-<?=$activation->id?>" aria-controls="tab-<?=$activation->id?>" data-toggle="tab" role="tab"
               class="btn_stol w-button <?=$a ? 'on' : ''?>" aria-selected="<?=$a ? 'true' : 'false'?>">
                <?=$activation->clone == 0 ? Yii::t('account', 'Main place') : Yii::t('account', 'C') . $activation->clone?>
                <?=$activation->clone == 0 || $activation->isUsed($id) ? '' : '<b class="text-danger" title="' . Yii::t('account', 'Stop list') . '">!</b>'?>
            </a>
            <?php $a = false ?>
            <?php if($activation->clone){
                $c++;
            } ?>
        <?php endforeach ?>
        <?php if($id == 1 && $c < 2): ?>
            <a href="<?=Url::to(['/pm/activation/index'])?>" class="btn_stol w-button btn btn-success"><?=Yii::t('account', 'Buy clone')?></a>
        <?php endif ?>
    </div>
    <div class="tab-content">
        <?php $a = true; ?>
        <?php foreach($user->activeActivations as $activation): ?>
            <?php if($activation->table < $id || $activation->start > $id){
                continue;
            } ?>
            <div id="tab-<?=$activation->id?>" class="mt-3 tab-pane <?=$a ? 'active' : ''?>">
                <div class="block_sapfir">
                    <?php $a = false ?>
                    <div class="wrapp_crystal_top">
                        <div class="w-inline-block text-center">
                            <img src="<?=$imageV?>" loading="lazy" class="crystal_top"/><br/>
                            <div class="btn btn-default p-3 white-background shadow-sm">
                                <?=Yii::$app->user->identity->id == $user->id ? Yii::t('account', 'You') : Yii::t('account', 'Partner')?>:
                                <b><?=$user->username . ($activation->clone ? ' (' . Yii::t('account', 'C') . $activation->clone . ')' : '')?></b>
                            </div>
                        </div>
                    </div>
                    <div class="div-block-19">
                        <div class="link-block w-inline-block text-center">
                            <img src="<?=$image?>" loading="lazy" class="crystal_center"/><br/>
                            <div class="btn btn-default p-3 white-background shadow-sm select-button" <?php if(!$activation->{"t{$id}Left"}): ?>data-table="<?=$id?>" data-at="<?=$activation->id?>" data-side="0"<?php endif ?>>
                                <?php if($activation->{"t{$id}Left"}): ?>
                                    <?php if(!$activation->{"t{$id}Left"}->user): ?>
                                        <?=Yii::t('account', 'Deleted user')?>
                                    <?php else: ?>
                                        <b><a href="?search=<?=$activation->{"t{$id}Left"}->user->username?>"><?=$activation->{"t{$id}Left"}->user->username
                                                . ($activation->{"t{$id}Left"}->clone ? ' (' . Yii::t('account', 'C') . $activation->{"t{$id}Left"}->clone . ')' : '')?></a></b>
                                    <?php endif ?>
                                <?php else: ?>
                                    <?=Yii::t('account', 'Free place')?>
                                <?php endif ?>
                            </div>
                        </div>
                        <div class="w-inline-block text-center">
                            <img src="<?=$image?>" loading="lazy" class="crystal_center _2"/><br/>
                            <div class="btn btn-default p-3 white-background shadow-sm select-button" <?php if(!$activation->{"t{$id}Right"}): ?>data-table="<?=$id?>" data-at="<?=$activation->id?>" data-side="1"<?php endif ?>>
                                <?php if($activation->{"t{$id}Right"}): ?>
                                    <?php if(!$activation->{"t{$id}Right"}->user): ?>
                                        <?=Yii::t('account', 'Deleted user')?>
                                    <?php else: ?>
                                        <b><a href="?search=<?=$activation->{"t{$id}Right"}->user->username?>"><?=$activation->{"t{$id}Right"}->user->username
                                                . ($activation->{"t{$id}Right"}->clone ? ' (' . Yii::t('account', 'C') . $activation->{"t{$id}Right"}->clone . ')' : '')?></a></b>
                                    <?php endif ?>
                                <?php else: ?>
                                    <?=Yii::t('account', 'Free place')?>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                    <div class="div-block-20">
                        <div class="link-block-17 w-inline-block text-center">
                            <img src="<?=$image?>" loading="lazy" class="crystal_down"/><br/>
                            <div class="btn btn-default p-3 white-background shadow-sm select-button"
                                 <?php if($activation->{"t{$id}Left"} && !$activation->{"t{$id}Left"}->{"t{$id}Left"}): ?>data-table="<?=$id?>" <?=$activation->{"t{$id}Left"} ? 'data-at="' . $activation->{"t{$id}_left"} . '"' : ''?>
                                 data-side="0"<?php endif ?>>
                                <?php if($activation->{"t{$id}Left"} && $activation->{"t{$id}Left"}->{"t{$id}Left"}): ?>
                                    <?php if(!$activation->{"t{$id}Left"}->{"t{$id}Left"}->user): ?>
                                        <?=Yii::t('account', 'Deleted user')?>
                                    <?php else: ?>
                                        <b><a href="?search=<?=$activation->{"t{$id}Left"}->{"t{$id}Left"}->user->username?>"><?=$activation->{"t{$id}Left"}->{"t{$id}Left"}->user->username
                                                . ($activation->{"t{$id}Left"}->{"t{$id}Left"}->clone ? ' (' . Yii::t('account', 'C') . $activation->{"t{$id}Left"}->{"t{$id}Left"}->clone . ')' : '')?></a></b>
                                    <?php endif ?>
                                <?php else: ?>
                                    <?=Yii::t('account', 'Free place')?>
                                <?php endif ?>
                            </div>
                        </div>
                        <div class="link-block-17 w-inline-block text-center">
                            <img src="<?=$image?>" loading="lazy" class="crystal_down"/><br/>
                            <div class="btn btn-default p-3 white-background shadow-sm select-button"
                                 <?php if($activation->{"t{$id}Left"} && !$activation->{"t{$id}Left"}->{"t{$id}Right"}): ?>data-table="<?=$id?>" <?=$activation->{"t{$id}Left"} ? 'data-at="' . $activation->{"t{$id}_left"} . '"' : ''?>
                                 data-side="1"<?php endif ?>>
                                <?php if($activation->{"t{$id}Left"} && $activation->{"t{$id}Left"}->{"t{$id}Right"}): ?>
                                    <?php if(!$activation->{"t{$id}Left"}->{"t{$id}Right"}->user): ?>
                                        <?=Yii::t('account', 'Deleted user')?>
                                    <?php else: ?>
                                        <b><a href="?search=<?=$activation->{"t{$id}Left"}->{"t{$id}Right"}->user->username?>"><?=$activation->{"t{$id}Left"}->{"t{$id}Right"}->user->username
                                                . ($activation->{"t{$id}Left"}->{"t{$id}Right"}->clone ? ' (' . Yii::t('account', 'C') . $activation->{"t{$id}Left"}->{"t{$id}Right"}->clone . ')' : '')?></a></b>
                                    <?php endif ?>
                                <?php else: ?>
                                    <?=Yii::t('account', 'Free place')?>
                                <?php endif ?>
                            </div>
                        </div>
                        <div class="link-block-17 w-inline-block text-center">
                            <img src="<?=$image?>" loading="lazy" class="crystal_down"/><br/>
                            <div class="btn btn-default p-3 white-background shadow-sm select-button"
                                 <?php if($activation->{"t{$id}Right"} && !$activation->{"t{$id}Right"}->{"t{$id}Left"}): ?>data-table="<?=$id?>" <?=$activation->{"t{$id}Right"} ? 'data-at="' . $activation->{"t{$id}_right"} . '"' : ''?>
                                 data-side="0"<?php endif ?>>
                                <?php if($activation->{"t{$id}Right"} && $activation->{"t{$id}Right"}->{"t{$id}Left"}): ?>
                                    <?php if(!$activation->{"t{$id}Right"}->{"t{$id}Left"}->user): ?>
                                        <?=Yii::t('account', 'Deleted user')?>
                                    <?php else: ?>
                                        <b><a href="?search=<?=$activation->{"t{$id}Right"}->{"t{$id}Left"}->user->username?>"><?=$activation->{"t{$id}Right"}->{"t{$id}Left"}->user->username
                                                . ($activation->{"t{$id}Right"}->{"t{$id}Left"}->clone ? ' (' . Yii::t('account', 'C') . $activation->{"t{$id}Right"}->{"t{$id}Left"}->clone . ')' : '')?></a></b>
                                    <?php endif ?>
                                <?php else: ?>
                                    <?=Yii::t('account', 'Free place')?>
                                <?php endif ?>
                            </div>
                        </div>
                        <div class="link-block-17 w-inline-block text-center">
                            <img src="<?=$image?>" loading="lazy" class="crystal_down"/><br/>
                            <div class="btn btn-default p-3 white-background shadow-sm select-button"
                                <?php if($activation->{"t{$id}Right"} && !$activation->{"t{$id}Right"}->{"t{$id}Right"}): ?><?=$activation->{"t{$id}Right"} ? 'data-at="' . $activation->{"t{$id}_right"} . '"' : ''?>
                                    data-side="1"<?php endif ?>>
                                <?php if($activation->{"t{$id}Right"} && $activation->{"t{$id}Right"}->{"t{$id}Right"}): ?>
                                    <?php if(!$activation->{"t{$id}Right"}->{"t{$id}Right"}->user): ?>
                                        <?=Yii::t('account', 'Deleted user')?>
                                    <?php else: ?>
                                        <b><a href="?search=<?=$activation->{"t{$id}Right"}->{"t{$id}Right"}->user->username?>"><?=$activation->{"t{$id}Right"}->{"t{$id}Right"}->user->username
                                                . ($activation->{"t{$id}Right"}->{"t{$id}Right"}->clone ? ' (' . Yii::t('account', 'C') . $activation->{"t{$id}Right"}->{"t{$id}Right"}->clone . ')' : '')?></a></b>
                                    <?php endif ?>
                                <?php else: ?>
                                    <?=Yii::t('account', 'Free place')?>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
    <div class="modal" id="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?=Yii::t('account', 'Select invited user')?> </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <select name="user" id="user-selection" class="form-control">
                        <option value=""></option>
                        <?php foreach($users as $u): ?>
                            <option value="<?=$u->id?>"><?=$u->username?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="save-user"><?=Yii::t('account', 'Save')?></button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=Yii::t('account', 'Close')?></button>
                </div>
            </div>
        </div>
    </div>
</div>