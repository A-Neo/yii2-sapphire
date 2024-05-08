<?php

/* @var $this yii\web\View */
/* @var $news app\models\News */

$this->title = $news->t_title;
$this->registerMetaTag(['name' => 'title', 'content' => $news->t_seo_title], 'title');
$this->registerMetaTag(['name' => 'description', 'content' => $news->t_seo_description], 'description');
$this->registerMetaTag(['name' => 'keywords', 'content' => $news->t_seo_keywords], 'keywords');
$this->params['breadcrumbs'][] = ['label' => Yii::t('site', 'News'), 'url' => ['/pm/news/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php

use yii\helpers\Url;

/**
 * @var $model \app\models\News
 */
?>
<div class="content">
    <h1><?=$this->t_title?></h1>
    <i><?=$news->published_at?></i><br/>
    <?=$news->t_text?>
</div>
