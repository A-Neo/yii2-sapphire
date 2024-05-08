<?php

use app\assets\FrontendAsset;
use rmrevin\yii\fontawesome\FAS;
use rmrevin\yii\fontawesome\FAB;
use yii\bootstrap4\ActiveForm;
use Baha2Odeh\RecaptchaV3\RecaptchaV3Widget;

/**
 * @var $model \app\models\ContactForm
 */

$asset = $this->assetBundles[FrontendAsset::class];
?>
<div class="container">
    <h1 class="main-header">
        <?= Yii::$app->settings->get('contact', 'ContactTitle', [
            'en' => 'Contact us',
            'ru' => 'Обратная связь',
            'uk' => 'Зворотній звьязок',
        ]) ?>
    </h1>
    <?php $form = ActiveForm::begin(['validateOnBlur' => false]); ?>
    <div class="d-flex justify-content-between contacts">
        <div class="contacts-item">
            <h2>
                <?= Yii::$app->settings->get('contact', 'ContactSubTitle', [
                    'en' => 'River Coins Wallet',
                    'ru' => 'River Coins Wallet',
                    'uk' => 'River Coins Wallet',
                ]); ?>
            </h2>
            <?php if ($value = Yii::$app->settings->get('contact', 'email', 'rivercoinsService@mail.com')): ?>
                <div class="contact d-flex align-items-center">
                    <?= FAS::icon('envelope') ?>
                    <p>
                        <?= $value ?>
                    </p>
                </div>
            <?php endif ?>
            <?php if ($value = Yii::$app->settings->get('contact', 'skype', 'river-coins-wallet')): ?>
                <div class="contact d-flex align-items-center">
                    <?= FAB::icon('skype') ?>
                    <p>
                        <?= $value ?>
                    </p>
                </div>
            <?php endif ?>
            <?php if ($value = Yii::$app->settings->get('contact', 'telegram', '@river-coins-wallet')): ?>
                <div class="contact d-flex align-items-center">
                    <?= FAB::icon('telegram') ?>
                    <p>
                        <a href="<?= $value ?>" target="_blank"><?= Yii::$app->settings->get('contact', 'TelegramLinkTitle', [
                                'en' => 'Subscribe',
                                'ru' => 'Subscribe',
                                'uk' => 'Subscribe',
                            ]); ?></a>
                    </p>
                </div>
            <?php endif ?>
            <?php if ($value = Yii::$app->settings->get('contact', 'phone', '+380 44 392 13 68')): ?>
                <div class="contact d-flex align-items-center">
                    <?= FAS::icon('phone') ?>
                    <p>
                        <?= $value ?>
                    </p>
                </div>
            <?php endif ?>
            <div class="soc-netw d-flex align-items-center">
                <?php foreach (['instagram', 'facebook', 'telegram', 'youtube', 'twitter', 'vk'] as $social): ?>
                    <?php if ($value = Yii::$app->settings->get('contact', $social, '#')): ?>
                        <a href="<?= $value ?>">
                            <?= FAB::icon($social) ?>
                        </a>
                    <?php endif ?>
                <?php endforeach ?>
            </div>
        </div>
        <div class="contacts-item">
            <?= $form->field($model, 'name')->textInput(['readonly' => !Yii::$app->user->isGuest && !empty($model->name), 'placeholder' => Yii::$app->settings->get('contact', 'placeholderName', [
                'en' => 'John John',
                'ru' => 'Иван иванов',
                'uk' => 'Іван Іванов',
            ])]) ?>
            <div class="border-input"></div>
            <?= $form->field($model, 'email')->textInput(['readonly' => !Yii::$app->user->isGuest && !empty($model->email), 'placeholder' => Yii::$app->settings->get('contact', 'placeholderEmail', 'mail@gmail.com')]) ?>
            <div class="border-input"></div>
            <?= $form->field($model, 'phone')->textInput(['readonly' => !Yii::$app->user->isGuest && !empty($model->phone), 'placeholder' => Yii::$app->settings->get('contact', 'placeholderPhone', '+00(000)-00-00-000')]) ?>
            <div class="border-input"></div>
        </div>
        <div class="contacts-item">
            <div class="textarea-header d-flex align-items-center">
                <img src="<?= $asset->image('7u6tgVector.png') ?>">
                <p>
                    <?= Yii::t('site', 'Your message or question to the support service') ?>
                </p>
            </div>
            <div class="textarea-cont position-relative">
                <img src="<?= $asset->image('87iuySubtract.png') ?>" class="position-absolute">
                <?= $form->field($model, 'question')->textarea()->label(false); ?>
            </div>
            <?= $form->field($model, 'code')->widget(RecaptchaV3Widget::class); ?>
            <button class="position-relative d-flex align-items-center justify-content-center">
                <img src="<?= $asset->image('yabutton.png') ?>">
            </button>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <div id="map" class="map" style="width: 100%;height: 400px"></div>
    <script
            src="https://maps.googleapis.com/maps/api/js?key=<?= Yii::$app->settings->get('contact', 'mapApiKey', 'AIzaSyAghdqXxqZYLHRhNmCS3twkaXkcli5nXCY') ?>&callback=initMap&libraries=&v=weekly"
            async
    ></script>
    <script type="text/javascript">
        function initMap() {
            // The location of Uluru
            const uluru = {
                lat: <?= Yii::$app->settings->get('contact', 'mapLatitude', '50.44') ?>,
                lng: <?= Yii::$app->settings->get('contact', 'mapLongitude', '30.54') ?>
            };
            // The map, centered at Uluru
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: uluru,
            });
            // The marker, positioned at Uluru
            const marker = new google.maps.Marker({
                position: uluru,
                map: map,
            });
        }
    </script>