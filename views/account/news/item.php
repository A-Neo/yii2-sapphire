<?php

use yii\helpers\Url;

/**
 * @var $model \app\models\News
 */
?>
<h3><?=$model->t_title?></h3>
<div class="content">
    <i><?=$model->published_at?></i><br/>
    <?=$model->t_short?><br/>
    <div class="pull-right"><a href="<?=Url::to(['/pm/news/view', 'id' => $model->id])?>"><?=Yii::t('site', 'Read more ...')?></a></div>
</div>
