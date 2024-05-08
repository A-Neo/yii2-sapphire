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
$this->title = Yii::t('account', 'Shop');

?>
<div class="w-100 wrapper_form-balance">
    <p class="text-left"><a href="/pm/emerald">Emerald Health</a></p>
    <table class="table table-striped">
        <tr>
            <th>№</th>
            <th>Product</th>
            <th>Comment</th>
        </tr>
        <tr>
            <th>1</th>
            <th>Пластырь</th>
            <th>Только в подарок</th>
        </tr>
    </table>

</div>