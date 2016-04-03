<?

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '3mGZIzgKVhcehvhxfUB2N3PO7GUS8fr4',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'class'              => 'app\base\web\UserWeb',
            'identityClass'      => 'app\models\User',
            'loginUrl'           => ['auth/authorisation/login'],
            'enableAutoLogin'    => true,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'db' => require(__DIR__ . '/../../base.db.php'),
            'defaultRoles' => ['user', 'admin'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/../../base.db.php'),
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'showScriptName' => false,
            'enablePrettyUrl' => true,
            'rules' => require(__DIR__ . '/routes.php'),
        ],
        'mainMenu' => [
            'class' => 'app\base\components\Menu',
            'items' => require(__DIR__ . '/../menu/mainMenuItems.php'),
        ],
        'adminMenu' => [
            'class' => 'app\base\components\Menu',
            'items' => require(__DIR__ . '/../menu/adminMenuItems.php'),
        ],
    ],
    'params' => $params,
    'modules' => [
        'auth' => [
            'class' => 'app\modules\auth\Module',
        ],
        'cabinet' => [
            'class' => 'app\modules\cabinet\Module',
        ]
    ]
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [
            'crud' => [
                'class' => 'yii\gii\generators\crud\Generator', 'baseControllerClass' => 'app\base\web\Controller'
            ],
            'module' => [
                'class' => 'app\generators\module\Generator', 'baseControllerClass' => 'app\base\web\Controller'
            ],
            'model' => [
                'class' => 'app\generators\model\Generator', 'baseClass' => 'app\base\db\ActiveRecord', 'queryBaseClass' => 'app\base\db\ActiveQuery'
            ],
        ]
    ];
}

return $config;
