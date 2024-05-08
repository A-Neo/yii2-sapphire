<?php


$domain = '.' . $_SERVER['HTTP_HOST'];
return [
    'bootstrap'  => ['languagesDispatcher'],
    'components' => [
        'request'             => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'bsdafgsaddasdfy5NfdslMaodsm355EHELd',
            'enableCsrfCookie'    => false,
            'ipHeaders'           => [
                'X-Forwarded-For',
                'X-Real-Ip',
                'true-client-ip',
                'cf-connecting-ip',
            ],
        ],
        'user'                => [
            'class'           => 'yii\web\User',
            'identityClass'   => 'app\models\User',
            'enableAutoLogin' => true,
            'autoRenewCookie' => true,
            'identityCookie'  => ['name' => 'sapphire-identity', 'httpOnly' => true, 'secure' => true, 'domain' => $domain],
            'authTimeout'     => 3600 * 4, // auth expire
            'accessChecker'   => 'app\components\AccessChecker',
        ],
        'session'             => [
            //'class'        => 'yii\redis\Session',
            'class'        => 'yii\web\Session',
            'name'         => 'sapphire-session-id',
            'cookieParams' => ['httponly' => true, 'secure' => true, 'lifetime' => 3600 * 4, 'domain' => $domain],
            'timeout'      => 3600 * 4, //session expire
            'useCookies'   => true,
        ],
        'errorHandler'        => [
            'errorAction' => 'site/error',
        ],
        'response'            => [
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class'       => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG, // use "pretty" output in debug mode
                ],
            ],
        ],
        'languagesDispatcher' => include 'languagesDispatcher.php',
        'assetManager'        => [
            'class'           => '\yii\web\AssetManager',
            'linkAssets'      => true,
            'appendTimestamp' => true,
            'bundles'         => [
                'kartik\editors\assets\SummernoteAsset'            => [
                    'depends' => [
                        'app\assets\AdminAsset',
                    ],
                ],
                'kartik\editors\assets\KrajeeSummernoteAsset'      => [
                    'depends' => [
                        'app\assets\AdminAsset',
                    ],
                ],
                'kartik\editors\assets\KrajeeSummernoteStyleAsset' => [
                    'depends' => [
                        'app\assets\AdminAsset',
                    ],
                ],
            ],
        ],
        'urlManager'          => [
            'class'           => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'normalizer'      => [
                'class'                  => 'yii\web\UrlNormalizer',
                'collapseSlashes'        => true,
                'normalizeTrailingSlash' => true,
            ],
            'rules'           => [
                '/' => 'site/login',
                [
                    'pattern' => '/',
                    'route'   => 'site/index',
                ],
                /*
                                '<language:[\w]{2}+>/<action:[\w\-]+>/<id:[\-\d]+>'                      => 'site/<action>',
                                '<language:[\w]{2}+>/<action:[\w\-]+>'                                   => 'site/<action>',
                                '<language:[\w]{2}+>/<controller:[\w\-]+>/<action:[\w\-]+>/<id:[\-\d]+>' => '<controller>/<action>',
                                '<language:[\w]{2}+>/<controller:[\w\-]+>/<action:[\w\-]+>'              => '<controller>/<action>',
                */
                // ок, тогда можно админку переименовать ссылку в
                // https://sapphire-gr.com/cp  такого  логина точно не будет
                // а личный кабинет например https://sapphire-gr.com/pm
                //

                '<module:(pm|cp)>/<id:[\-\d]+>'                                                => '<module>/default/index',
                '<module:(pm|cp)>'                                                             => '<module>/default/index',
                '<module:(pm|cp)>/<controller:[\w\-]+>/<n:[\d]+>/<id:[\-\d]+>'                 => '<module>/<controller>/index',
                '<module:(pm|cp)>/<controller:[\w\-]+>/<id:[\-\d]+>.html'                      => '<module>/<controller>/view',
                '<module:(pm|cp)>/<controller:[\w\-]+>/<slug:[\w\-]+>.html'                    => '<module>/<controller>/show',
                '<module:(pm|cp)>/<controller:[\w\-]+>/<id:[\-\d]+>'                           => '<module>/<controller>/index',
                '<module:(pm|cp)>/<controller:[\w\-]+>'                                        => '<module>/<controller>/index',
                '<module:(pm|cp)>/<controller:[\w\-]+>/<action:[\w\-]+>/<slug:[\w\-\s]+>.html' => '<module>/<controller>/<action>',
                '<module:(pm|cp)>/<controller:[\w\-]+>/<action:[\w\-]+>/<id:[\-\d]+>'          => '<module>/<controller>/<action>',
                '<module:(pm|cp)>/<controller:[\w\-]+>/<action:[\w\-]+>'                       => '<module>/<controller>/<action>',

                '<slug:[\w\-]+>.html' => 'page/show',

                '<action:(logout|request-password-reset|reset-password|verify-email|resend-verification-email)>/<id:[\d]+>'     => 'site/<action>',
                '<action:(logout|request-password-reset|reset-password|verify-email|resend-verification-email)>/<slug:[\w\-]+>' => 'site/<action>',
                '<action:(logout|request-password-reset|reset-password|verify-email|resend-verification-email)>'                => 'site/<action>',
                '<slug:[\w\-]+>'                                                                                                => 'site/signup',
                'signup'                                                                                                        => 'site/signup',

                '<controller:[\w\-]+>/<id:[\-\d]+>.html'             => '<controller>/view',
                '<controller:[\w\-]+>/<slug:[\w\-]+>.html'           => '<controller>/show',
                '<controller:[\w\-]+>/<page:[\d]+>'                  => '<controller>/index',
                '<controller:[\w\-]+>'                               => '<controller>/index',
                '<controller:[\w\-]+>/<action:[\w\-]+>/<page:[\d]+>' => '<controller>/<action>',
                '<controller:[\w\-]+>/<action:[\w\-]+>'              => '<controller>/<action>',

            ],
        ],
        'partnership' => [
            'class' => 'komer45\partnership\Partnership'
        ],
    ],
    'modules'    => [
        'pm' => [
            'class' => 'app\modules\account\Module',
        ],
        'cp' => [
            'class' => 'app\modules\admin\Module',
        ],
        'partnership' => [
            'class' => 'komer45\partnership\Module',
            'adminRoles' => ['superadmin', 'administrator'],
        ],
    ],
];
