<?php

use yii\helpers\Url;
use rmrevin\yii\fontawesome\FAS;
use app\components\TextProcessor;

/* @var $this yii\web\View */
/* @var $page app\models\Page */

$this->title = $page->t_title;
$this->registerMetaTag(['name' => 'title', 'content' => $page->t_seo_title], 'title');
$this->registerMetaTag(['name' => 'description', 'content' => $page->t_seo_description], 'description');
$this->registerMetaTag(['name' => 'keywords', 'content' => $page->t_seo_keywords], 'keywords');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wrapper_form-in">
    <h1><?=$this->title?></h1>
    <?=TextProcessor::process($page->t_text)?>
</div>
