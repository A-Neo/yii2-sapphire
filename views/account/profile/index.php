<?php

use yii\widgets\DetailView;
use yii\bootstrap4\Html;
use app\components\Api;
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $user \app\models\User */

$this->title = Yii::t('account', 'Your profile');
$this->params['breadcrumbs'][] = $this->title;

?>
<?=$this->render('_tabs')?>
<div class="row w-100 mt-3">
    <div class="col-xs-12 col-md-6">
        <div class="wrapper_form-balance">
            <?=DetailView::widget([
                'model'      => $user,
                'attributes' => [
                    'username',
                    [
                        'attribute' => 'referer',
                        'label'     => Yii::t('account', 'Referrer'),
                        'value'     => $user->referrer ? $user->referrer->username : null,
                    ],
                    'username',
                    [
                        'attribute' => 'id_ref_emerald',
                        'label'     => 'Emerald Referer',
                        'value'     => $user->id_ref_emerald ? $user->id_ref_emerald->username : null,
                    ],
                    'email',
                    'phone',
                    'wallet',
                    'wallet_perfect',
                    'wallet_tether',
                    'wallet_banki_rf',
                    'wallet_dc',
                    [
                        'attribute' => 'balance',
                        'format'    => 'raw',
                        'value'     => Api::asNumber($user->balance - $user->accumulation),
                    ],
                    'full_name',
                    [
                        'attribute' => 'country',
                        'format'    => 'raw',
                        'value'     => function () use ($user) {
                            $countries = include_once ROOT_DIR . '/config/' . substr(Yii::$app->language, 0, 2) . '/countries.php';
                            $data = array_combine(array_column($countries, 'name'), array_column($countries, 'name'));
                            $countries = array_combine(array_column($countries, 'name'), array_values($countries));
                            $result = '';
                            if(isset($countries[$user->country])){
                                $result .= '<img class="flag" src="' . AppAsset::image('../flags/', false) . $countries[$user->country]['alpha2'] . '.png" height="24"/> ';
                            }
                            return $result . $user->country;
                        },
                    ],
                    'birth_date',
                    [
                        'attribute' => 'avatar',
                        'label'     => Yii::t('account', 'Avatar'),
                        'format'    => 'html',
                        'value'     => $user->avatar ? Html::img(['/pm/profile/avatar'], ['style' => 'height:200px']) : '',
                    ],
                ],
            ]);?>
        </div>
    </div>
</div>