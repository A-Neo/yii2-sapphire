<?php
/* @var $content string */

use yii\bootstrap4\Breadcrumbs;
use app\widgets\Alert;

?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <?php
                        if(!is_null($this->title)){
                            echo \yii\helpers\Html::encode($this->title);
                        }else{
                            echo \yii\helpers\Inflector::camelize($this->context->id);
                        }
                        ?>
                    </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <?=Breadcrumbs::widget([
                        'links'    => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                        'homeLink' => ['label' => Yii::t('yii', 'Home'), 'url' => '/' . Yii::$app->controller->module->id],
                        'options'  => [
                            'class' => 'breadcrumb float-sm-right',
                        ],
                    ]);?>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <?=Alert::widget()?>
        <?=$content?><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>