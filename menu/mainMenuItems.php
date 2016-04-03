<?
return [
    'user' => [
        [
            'label' => '{#user_login}',
            'items' => [
                ['label' => 'Cabinet',  'url' => ['/cabinet/home/index']],
                ['label' => 'Settings', 'url' => ['/cabinet/settings/index']],
                ['label' => '', 'options' => ['class' => 'divider']],
                ['label' => 'Logout', 'url' => ['/auth/authorisation/logout'], 'linkOptions' => ['data-method' => 'post']],
            ]
        ],
    ],
    '@' => [
        [
            'label' => 'Logout',
            'url' => ['/auth/authorisation/logout'],
            'linkOptions' => ['data-method' => 'post']
        ]
    ],
    '?' => [
        [
            'label' => 'Login', 'url' => ['/auth/authorisation/login']
        ],
        [
            'label' => 'Sign up', 'url' => ['/auth/registration/index']
        ]
    ],
    '*' => [

    ]
];