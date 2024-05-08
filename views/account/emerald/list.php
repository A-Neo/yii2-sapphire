<?php
use app\models\EmeraldMain;

/* @var $this yii\web\View
 * @var $levels EmeraldMain[]
 * @var $username string
 * @var $userid integer
 * @var $level integer
 */

?>

<div class="container">
    <div class="emerald_level_container">
    <?php if (isset($levels[$level])):
    $levelUsers = $levels[$level]->getUsers();
    ?>
        <div class="row justify-content-center mb-5">
            <div class="col-md-2">
                <div class="travel_login_block_avatar">
                    <img src="/pm/emerald/user-avatar?uid=<?= $userid ?>" alt="Avatar" class="rounded-circle">
                </div>
                <div class="travel_login_block"><?= $username ?></div>
            </div>
        </div>
        <div class="row justify-content-center mb-5">
            <?php for($j = 1; $j <= 3; $j++) : $user = array_shift($levelUsers); ?>
                <div class="col-md-2 justify-content-center">
                    <?php if ($user):  ?>
                        <div class="travel_login_block_avatar">
                            <img src="/pm/emerald/user-avatar?uid=<?= $user->id_user ?>" alt="Avatar" class="rounded-circle">
                        </div>
                        <div class="travel_login_block js-travel-open" data-uid="<?= $user->id_user ?>" data-level="<?= $level ?>"><?= $user->getUsername() ?></div>
                    <?php else:  ?>
                        <div class="travel_login_block_avatar">
                            <img src="/pm/emerald/user-avatar?uid=-1" alt="Avatar" class="rounded-circle">
                        </div>
                        <div class="travel_login_block">Не занято</div>
                    <?php endif;  ?>
                </div>
            <?php endfor; ?>
        </div>
        <div class="row justify-content-left">
            <?php while($user = array_shift($levelUsers)): ?>
                <div class="col-md-2 justify-content-center  mb-5">
                    <div class="travel_login_block_avatar">
                        <img src="/pm/emerald/user-avatar?uid=<?= $user->id_user ?>" alt="Avatar" class="rounded-circle">
                    </div>
                    <div class="travel_login_block js-travel-open" data-uid="<?= $user->id_user ?>" data-level="<?= $level ?>"><?= $user->getUsername() ?></div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        Этот уровень пока недоступен
    <?php endif; ?>
    </div>
</div>

