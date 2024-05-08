<?php

return [
    'id'         => 'sapphire',
    'name'       => 'Sapphire',
    'language'   => 'ru-RU',
    'basePath'   => dirname(__DIR__),
    'bootstrap'  => /*YII_DEBUG ? ['log', 'gii', 'debug'] : */
        ['log', /*, 'settings'*/],
    'aliases'    => [
        '@root'  => dirname(__DIR__),
        '@app'   => dirname(__DIR__) . '/app',
        '@views' => dirname(__DIR__) . '/views',
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(__DIR__) . '/vendor',
    'container'  => [
        'definitions' => [
            'yii\web\View'                       => 'app\components\View',
            'yii\helpers\Html'                   => 'yii\bootstrap4\Html',
            'yii\bootstrap\Html'                 => 'yii\bootstrap4\Html',
            'yii\bootstrap\BootstrapAsset'       => 'yii\bootstrap4\BootstrapAsset',
            'yii\bootstrap\BootstrapPluginAsset' => 'yii\bootstrap4\BootstrapPluginAsset',
            'yii\grid\ActionColumn'              => 'app\components\columns\ActionColumn',
            'yii\grid\SerialColumn'              => 'app\components\columns\SerialColumn',
            'yii\grid\DataColumn'                => 'app\components\columns\DataColumn',
            'yii\bootstrap4\Tabs'                => 'app\widgets\Tabs',
            'yii\bootstrap4\Nav'                 => 'app\widgets\Nav',
            //'yii\widgets\ActiveForm'             => 'yii\bootstrap4\ActiveForm',
            //'yii\widgets\ActiveField'            => 'yii\bootstrap4\ActiveField',
        ],
    ],
    'components' => [
        'db'        => [
            'class'             => 'yii\db\Connection',
            // 'dsn'               => YII_DEBUG ? 'mysql:host=localhost;port=3306;dbname=sapphire' : 'mysql:host=127.0.0.1;port=3306;dbname=omercans_feng532',
            // 'username'          => YII_DEBUG ? 'sapphire' : 'omercans_feng532',
            // 'password'          => YII_DEBUG ? 'sapphire' : 'K6RYbLkXW8Dw',
            'dsn'               => 'mysql:host=127.0.0.1;port=3306;dbname=omercans_feng532',
            'username'          => 'root',
            'password'          => 'root',
            'charset'           => 'utf8',
            'attributes'        => [PDO::ATTR_CASE => PDO::CASE_NATURAL],
            'enableSchemaCache' => true,
        ],
        'cache'     => [
            //'class' => 'yii\redis\Cache',
            'class' => 'yii\caching\FileCache',
            //'keyPrefix' => 'RIVER_COINS_CACHE_',
        ],
        /*'redis'     => [
            'class' => 'yii\redis\Connection',
        ],*/
        'settings'  => [
            'class' => 'app\components\Settings',
        ],
        'menus'     => [
            'class' => 'app\components\Menus',
        ],
        'mailer'    => [
            'class'     => 'yii\swiftmailer\Mailer',
            'viewPath'  => '@root/views/mail',
            /*
            'transport' => [
                'class'         => 'Swift_SmtpTransport',
                'host'          => 'mail.sapphire-gr.com',
                'username'      => 'noreply@sapphire-gr.com',
                'password'      => 'r1Fi1wHgFrym9SvP',
                'port'          => '465',
                'encryption'    => 'tls',
                'StreamOptions' => [
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                    ],
                ],
            ],
            */
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
            //'enableSwiftMailerLogging' => YII_DEBUG,
        ],
        'log'       => [
            'class'      => 'yii\log\Dispatcher',
            // 'traceLevel' => YII_DEBUG ? 3 : 0,
            'traceLevel' => 3,
            'targets'    => [
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class'   => 'yii\log\FileTarget',
                    'levels'  => ['error', 'warning'],
                    'logVars' => [], //'_POST', '_GET', '_COOKIE', '_SESSION'],
                ],
            ],
        ],
        'formatter' => [
            'class' => 'app\components\Formatter',
        ],
        'i18n'      => [
            'translations' => [
                'yii'              => [
                    'class'          => 'yii\i18n\PhpMessageSource',
                    'basePath'       => '@yii/messages',
                    'sourceLanguage' => 'en-US',
                ],
                'yii2tech-admin'   => [
                    'class'    => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@yii2tech/admin/messages',
                ],
                'yii2mod.settings' => [
                    'class'    => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@yii2mod/settings/messages',
                ],
                '*'                => [
                    'class'          => 'yii\i18n\PhpMessageSource',
                    'basePath'       => '@app/messages',
                    'sourceLanguage' => 'en-US',
                ],
            ],
        ],
    ],
    'params'     => \yii\helpers\ArrayHelper::merge(!empty($envs['params']) ? $envs['params'] : [], [
        'bsVersion' => '4.x',
    ], include_once __DIR__ . '/params.php', file_exists(__DIR__ . '/params-local.php') ? include_once __DIR__ . '/params-local.php' : []),
    /*'modules'    => YII_DEBUG ? [
        'debug' => [
            'class'      => 'yii\debug\Module',
            // uncomment the following to add your IP if you are not connecting from localhost.
            'allowedIPs' => ['127.0.0.1', '::1'],
        ],
        'gii'   => [
            'class'      => 'yii\gii\Module',
            // uncomment the following to add your IP if you are not connecting from localhost.
            'allowedIPs' => ['127.0.0.1', '::1'],
        ],
    ] : [],*/
];
