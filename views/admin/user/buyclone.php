<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\widgets\DetailView;
use app\components\Api;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = Yii::t('admin', 'Buy clone for user:') . ' ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('admin', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create table">
    <div class="user-form">
        <?php $form = ActiveForm::begin(); ?>
        <p><?=Yii::t('admin', 'Funds for purchasing a clone will be taken from the user\'s balance. Top it up first if needed.')?></p>
        <?=DetailView::widget([
            'model'      => (object)[],
            'attributes' => [
                [
                    'attribute' => Yii::t('admin', 'Balance'),
                    'format'    => 'raw',
                    'value'     => Api::asNumber($model->balance - $model->accumulation),
                ],
                [
                    'attribute' => Yii::t('admin', 'Price'),
                    'format'    => 'raw',
                    'value'     => function () {
                        $attributes = [];
                        for($i = 1; $i <= 6; $i++){
                            $attributes[] = [
                                'attribute' => Yii::t('admin', 'Table') . ' ' . $i,
                                'format'    => 'raw',
                                'value'     => Api::asNumber(Yii::$app->settings->get('system', 'promotionAmount' . $i)),
                            ];
                        }
                        return DetailView::widget([
                            'model'      => (object)[],
                            'attributes' => $attributes,
                        ]);
                    },
                ],
            ],
        ])?>
        <div class="form-group required">
            <label for="tx-id"><?=Yii::t('account', 'Table')?></label>
            <select type="text" id="tx-table" class="form-control" name="table">
                <?php for($i = 1; $i <= 6; $i++): ?>
                    <option value="<?=$i?>"><?=Yii::t('admin', 'Table') . ' ' . $i . ' - ' . Api::asNumber(Yii::$app->settings->get('system', 'promotionAmount' . $i), false).'$';?></option>
                <?php endfor ?>
            </select>
            <div class="invalid-feedback"></div>
        </div>
        <div class="form-group">
            <?=Html::submitButton(Yii::t('admin', 'Buy'), ['class' => 'btn btn-success'])?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
