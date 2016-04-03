<?php
/**
 * Application configuration shared by all test types
 */
return [
    'language' => 'en-US',
    'aliases' => [
        '@test' => realpath(__DIR__. '/../unit'),
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\faker\FixtureController',
            'fixtureDataPath' => '@tests/codeception/fixtures',
            'templatePath' => '@tests/codeception/templates',
            'namespace' => 'tests\codeception\fixtures',
        ],
    ],
    'components' => [
        'db' => require(__DIR__ . '/db.php'),
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'db'    => require(__DIR__ . '/db.php'),
            'defaultRoles' => ['user', 'admin'],
        ],
        'mailer' => [
            'useFileTransport' => true,
        ],
    ],
];
