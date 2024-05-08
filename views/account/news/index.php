<?php

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = Yii::t('site', 'News');
$this->registerMetaTag(['name' => 'title', 'content' => Yii::$app->settings->get('news', 'SeoTitle', 'News')], 'title');
$this->registerMetaTag(['name' => 'description', 'content' => Yii::$app->settings->get('news', 'SeoDescription', 'News')], 'description');
$this->registerMetaTag(['name' => 'keywords', 'content' => Yii::$app->settings->get('news', 'SeoKeywords', 'News')], 'keywords');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content">
    <?=\yii\widgets\ListView::widget([
        'layout'       => "{items}\n{pager}",
        'dataProvider' => $dataProvider,
        'itemView'     => 'item',
    ])?>
</div>
