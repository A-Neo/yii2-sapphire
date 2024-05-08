<?php

use rmrevin\yii\fontawesome\FAS;
use app\components\Api;

$this->title = Yii::t('account', 'Dashboard');
/**
 * @var mixed $info
 */
?>
<div class="row">
    <?php foreach($info as $item): ?>
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 p-3">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-info"><?=$item['label']?></h3>
                    <h3><?=$item['value']?></h3>
                </div>
            </div>
        </div>
    <?php endforeach ?>
</div>
