<?

return [
    /**
     * Registration
     */
    'login'                          => 'auth/authorisation/login',
    'logout'                         => 'auth/authorisation/logout',
    'sign-up'                        => 'auth/registration/index',
    'forgot'                         => 'auth/forgot/index',
    'forgot/set-password/<code:\w+>' => 'auth/forgot/set-password',
    'forgot/set-password'            => 'auth/forgot/set-password',
];