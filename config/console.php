<?php

return [
    'enableCoreCommands'  => false,
    'controllerNamespace' => 'app\commands',
    'controllerMap'       => [
        'cache'         => [
            'class'         => 'yii\console\controllers\CacheController',
            'defaultAction' => 'flush-all',
        ],
        'migrate'       => 'yii\console\controllers\MigrateController',
    ],
];
