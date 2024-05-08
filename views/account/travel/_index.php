<?php
use app\models\Travel;
use yii\helpers\Url;

if ($level_info['level'] == 1 && $level_info['status'] == Travel::STATUS_UNPAID) {
    echo 'У вас первый уровень, необходимо внести депозит для активации';
?>   
    <a href="<?=Url::to(['/pm/travel/init'])?>" class="link_balance w-nav-link">Запустить</a>
<?
} else {
?>
    <div>Ваш текущий уровень <? echo $level_info['level'];?></div>
    <div>Голова стола: <?=$username;?></div>
    <div>Слот1: <?=($level_info->slotOne) ? $level_info->slotOne->username : '';?></div>
    <div>Слот2: <?=($level_info->slotTwo) ? $level_info->slotTwo->username : '';?></div>
    <div>Слот3: <?=($level_info->slotTree) ? $level_info->slotTree->username: '';?></div>
<?
}
?>