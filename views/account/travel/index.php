<?php
use app\models\TravelMain;
use app\models\TravelDelay;

/* @var $this yii\web\View
 * @var $levels TravelMain[]
 * @var $username string
 * @var $userid integer
 * @var $delayUsers TravelDelay[]
 */

//$this->title = 'Мои агенты [' . $username . ']';

$js = <<<JS

function show_travel_frame(id) {
    set_travel_click();
    $(id).show();
    $('.travel_frame_bg').show();
}

function hide_travel_frame() {
    $('.travel_frame').hide();
    $('.travel_frame_bg').hide();
}

function set_travel_click() {
    $('.js-travel-open').off('click');
    $('.js-travel-open').on('click', function(event) {
        var target = $(event.currentTarget);
        $.get('/pm/travel/net-list', 
              {uid: target.data('uid'), level: target.data('level')}, 
              function(data) {
                  $('#travel_frame_data').html(data);  
                  show_travel_frame('#matrix-modal');
              }, 'text');
    });

}

$(function() {
    $('.btn_travel_level').click(function(event) {
        var target = $(event.currentTarget);
        $('.btn_travel_level').removeClass('btn_travel_level_selected');
        target.addClass('btn_travel_level_selected');
        $('.travel_level_container').hide();
        $('#travel-level-' + target.data('level')).show();
    });
    
   set_travel_click();
   
    $('.travel_frame_bg').click(function() {
        hide_travel_frame();    
    });
    
    $('.travel_frame_close').click(function() {
        hide_travel_frame();    
    });
    
});
JS;

$this->registerJs($js, $this::POS_END);

$ok = Yii::$app->session->getFlash('okmessage', false);
$err = Yii::$app->session->getFlash('errmessage', false);

?>

<?php if($ok): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Поздравляем!</strong> <?= $ok ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>

<?php if($err): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Ошибка: </strong> <?= $err ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if(count($delayUsers) > 0): ?>
<div class="travel_delay_users_block">
    <?php while ($delayUser = array_shift($delayUsers)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Внимание! </strong> Пользователь <strong><?= $delayUser->getUserLogin() ?></strong> перешёл на стол
        №<?= $delayUser->level ?> раньше Вас! Если вы не поторопитесь и не откроете стол №<?= $delayUser->level ?>,
        то через <strong><?= date('H:i:s', $delayUser->date_end - time()) ?></strong> этот пользователь перейдет
        к Вашему рефереру!
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endwhile; ?>
</div>
<?php endif; ?>

<div class="container travel_main_block">
    <div class="mb-5">
        <button class="mb-3 btn_travel_level <?= (isset($levels[1]) ? 'btn_travel_level_active' : '') ?> btn_travel_level_selected" data-level="1">Стол 1</button>
        <button class="mb-3 btn_travel_level <?= (isset($levels[2]) ? 'btn_travel_level_active' : '') ?>" data-level="2">Стол 2</button>
        <button class="mb-3 btn_travel_level <?= (isset($levels[3]) ? 'btn_travel_level_active' : '') ?>" data-level="3">Стол 3</button>
        <button class="mb-3 btn_travel_level <?= (isset($levels[4]) ? 'btn_travel_level_active' : '') ?>" data-level="4">Стол 4</button>
        <button class="mb-3 btn_travel_level <?= (isset($levels[5]) ? 'btn_travel_level_active' : '') ?>" data-level="5">Стол 5</button>
        <button class="mb-3 btn_travel_level <?= (isset($levels[6]) ? 'btn_travel_level_active' : '') ?>" data-level="6">Стол 6</button>
        <button class="mb-3 btn_travel_level <?= (isset($levels[7]) ? 'btn_travel_level_active' : '') ?>" data-level="7">Стол 7</button>
        <button class="mb-3 btn_travel_level <?= (isset($levels[8]) ? 'btn_travel_level_active' : '') ?>" data-level="8">Стол 8</button>
    </div>
    <div class="container">
        <?php for($levelNum = 1; $levelNum <= 8; $levelNum++): ?>
            <div id="travel-level-<?= $levelNum ?>" class="travel_level_container" <?= ($levelNum > 1 ? 'style="display: none;"' : '') ?>>
                <?php if (isset($levels[$levelNum])):
                    $levelUsers = $levels[$levelNum]->getUsers();
                    ?>
                    <div class="row justify-content-center mb-5">
                        <div class="col-md-2">
                            <h3 style="font-size: 20px !important; text-align: center;">Мои агенты</h3>
                        </div>
                    </div>
                    <div class="row justify-content-center mb-5">
                        <?php for($j = 1; $j <= 3; $j++) : $user = array_shift($levelUsers); ?>
                            <div class="col-md-2 justify-content-center mb-2">
                            <?php if ($user):  ?>
                                <div class="travel_login_block_avatar">
                                    <img src="/pm/travel/user-avatar?uid=<?= $user->id_user ?>" alt="Avatar" class="rounded-circle">
                                </div>
                                <div class="travel_login_block js-travel-open" data-uid="<?= $user->id_user ?>" data-level="<?= $levelNum ?>"><?= $user->getUsername() ?></div>
                            <?php else:  ?>
                                <div class="travel_login_block_avatar">
                                    <img src="/pm/travel/user-avatar?uid=-1" alt="Avatar" class="rounded-circle">
                                </div>
                                <div class="travel_login_block travel_login_block_empty">Не занято</div>
                            <?php endif;  ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                    <div class="row justify-content-left">
                    <?php while($user = array_shift($levelUsers)): ?>
                        <div class="col-md-2 justify-content-center  mb-5">
                            <div class="travel_login_block_avatar">
                                <img src="/pm/travel/user-avatar?uid=<?= $user->id_user ?>" alt="Avatar" class="rounded-circle">
                            </div>
                            <div class="travel_login_block js-travel-open" data-uid="<?= $user->id_user ?>" data-level="<?= $levelNum ?>"><?= $user->getUsername() ?></div>
                        </div>
                    <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <?php if ($levelNum == 1): ?>
                        <a class="w-button btn_travel_active" href="/pm/travel/init">Активировать <?= $levelNum ?> уровень</a>
                    <?php else: ?>
                        Этот уровень пока недоступен
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endfor; ?>

    </div>
</div>


