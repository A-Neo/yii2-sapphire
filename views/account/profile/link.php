<?php

use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = Yii::t('account', 'Referral link');
$this->registerJs(<<<JS

function copyToClipboard(element) {
    var \$temp = $("<input>");
    $("body").append(\$temp);
    \$temp.val($(element).text()).select();
    document.execCommand("copy");
    \$temp.remove();
    alert("Ссылка скопирована: "+$(element).text());
}

JS
    , View::POS_END);
?>
<?=$this->render('_tabs')?>
<div class="row w-100 mt-3">
    <div class="col-xs-12 col-md-6">
        <div class="inform_tour w-inline-block w-100">
            <b><?=Yii::t('account', 'Referral link')?>:</b> <?=Html::a($url = Url::to(['/site/signup', 'slug' => Yii::$app->user->identity->username], true), $url, [
                'onClick' => 'copyToClipboard(this);return false;',
            ])?>
        </div>
    </div>
</div>
