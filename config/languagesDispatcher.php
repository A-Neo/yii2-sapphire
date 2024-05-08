<?php
return [
    'class'     => 'cetver\LanguagesDispatcher\Component',
    'languages' => ['ru', 'en', 'tr', 'de'],
    'handlers'  => [
        [
            // language for admin panel
            'class'    => 'app\components\AdminLanguageHandler',
        ],
        [
            // Detects a language from the query parameter.
            'class'      => 'cetver\LanguagesDispatcher\handlers\QueryParamHandler',
            'request'    => 'request', // optional, the Request component ID.
            'queryParam' => 'lang' // optional, the query parameter name that contains a language.
        ],
        [
            // Detects a language from the session.
            // Writes a language to the session, regardless of what handler detected it.
            'class'   => 'cetver\LanguagesDispatcher\handlers\SessionHandler',
            'session' => 'session', // optional, the Session component ID.
            'key'     => 'language' // optional, the session key that contains a language.
        ],
        [
            // Detects a language from the cookie.
            // Writes a language to the cookie, regardless of what handler detected it.
            'class'        => 'cetver\LanguagesDispatcher\handlers\CookieHandler',
            'request'      => 'request', // optional, the Request component ID.
            'response'     => 'response', // optional, the Response component ID.
            'cookieConfig' => [ // optional, the Cookie component configuration.
                                'class'    => 'yii\web\Cookie',
                                'name'     => 'language',
                                'domain'   => '',
                                'expire'   => strtotime('+1 year'),
                                'path'     => '/',
                                'secure'   => true | false, // depends on Request::$isSecureConnection
                                'httpOnly' => true,
            ],
        ],
        /*[ // TODO
            // Detects a language from an authenticated user.
            // Writes a language to an authenticated user, regardless of what handler detected it.
            // Note: The property "identityClass" of the "User" component must be an instance of "\yii\db\ActiveRecord"
            'class'             => 'cetver\LanguagesDispatcher\handlers\UserHandler',
            'user'              => 'user',  // optional, the User component ID.
            'languageAttribute' => 'language' // optional, an attribute that contains a language.
        ],*/
        [
            // Detects a language from the "Accept-Language" header.
            'class'   => 'cetver\LanguagesDispatcher\handlers\AcceptLanguageHeaderHandler',
            'request' => 'request', // optional, the Request component ID.
        ],
        [
            // Detects a language from the "language" property.
            'class'    => 'cetver\LanguagesDispatcher\handlers\DefaultLanguageHandler',
            'language' => 'ru' // the default language.
            /*
            or
            'language' => function () {
                return \app\models\Language::find()
                    ->select('code')
                    ->where(['is_default' => true])
                    ->createCommand()
                    ->queryScalar();
            },
            */
        ],
    ],
];
