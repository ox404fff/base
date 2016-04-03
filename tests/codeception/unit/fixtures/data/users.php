<?
/**
 * @var test\fixtures\UserFixture $this
 */

return [
    $this::ID_USER_ADMIN => [
        'id'         => $this::ID_USER_ADMIN,
        'type'       => app\models\User::TYPE_USER_ADMIN,
        'login'      => $this->getLogin($this::ID_USER_ADMIN),
        'password'   => '$2y$13$XsnlioA4cnZXFlmVsCQi2OOEGdCyfZJ7tveGnNU8P5kRK/3L0LJ8C', // polkilo
        'auth_key'   => 'auth_key_1',
        'created_at' => 1455353947,
        'updated_at' => 1455353947,
    ],
    $this::ID_USER => [
        'id'         => $this::ID_USER,
        'type'       => app\models\User::TYPE_USER,
        'login'      => $this->getLogin($this::ID_USER),
        'password'   => '$2y$13$Jw5asfZnqIAODgCLrtPJuOLaz.xTfXTOW6UPfBx9V5/MaxF6246WO', // polkilo1
        'auth_key'   => 'auth_key_2',
        'created_at' => 1455353947,
        'updated_at' => 1455353947,
    ],
    $this::ID_USER_INACTIVE_2 => [
        'id'         => $this::ID_USER_INACTIVE_2,
        'type'       => app\models\User::TYPE_INACTIVE,
        'login'      => $this->getLogin($this::ID_USER),
        'password'   => '$2y$13$Jw5asfZnqIAODgCLrtPJuOLaz.xTfXTOW6UPfBx9V5/MaxF6246WO', // polkilo1
        'auth_key'   => 'auth_key_21',
        'created_at' => 1455353947,
        'updated_at' => 1455353947,
    ],
    $this::ID_USER_DELETED => [
        'id'         => $this::ID_USER_DELETED,
        'type'       => app\models\User::TYPE_USER,
        'login'      => $this->getLogin($this::ID_USER_DELETED),
        'password'   => '$2y$13$pyH3F/JnbMZpDOmGVm6VX.wnd3Nn2yKDeb9Reyq2fzv5rZQPg.Gsu', // polkilo2
        'auth_key'   => 'auth_key_3',
        'is_deleted' => true,
        'deleted_at' => 1455353947,
        'created_at' => 1455353947,
        'updated_at' => 1455353947,
    ],
    $this::ID_USER_INACTIVE => [
        'id'         => $this::ID_USER_INACTIVE,
        'type'       => app\models\User::TYPE_INACTIVE,
        'login'      => $this->getLogin($this::ID_USER_INACTIVE),
        'password'   => '$2y$13$VMFy7NhkwJz2NmVkxsQU5eUW81Nq8czhe09tx74cIcujxKNK6oTDO', // polkilo3
        'auth_key'   => 'auth_key_4',
        'created_at' => 1455353947,
        'updated_at' => 1455353947,
    ],
    $this::ID_USER_INACTIVE_DELETED => [
        'id'         => $this::ID_USER_INACTIVE_DELETED,
        'type'       => app\models\User::TYPE_INACTIVE,
        'login'      => $this->getLogin($this::ID_USER_INACTIVE_DELETED),
        'password'   => '$2y$13$QTYhTJuIIchZPfaIx2sHCumoL91HEAqnbl4umD5pKhQnTnAGFIvrK', // polkilo4
        'auth_key'   => 'auth_key_5',
        'is_deleted' => true,
        'deleted_at' => 1455353947,
        'created_at' => 1455353947,
        'updated_at' => 1455353947,
    ],
    $this::ID_USER_INACTIVE_ADMIN_DUPLICATE => [
        'id'         => $this::ID_USER_INACTIVE_ADMIN_DUPLICATE,
        'type'       => app\models\User::TYPE_INACTIVE,
        'login'      => $this->getLogin($this::ID_USER_ADMIN),
        'password'   => '$2y$13$XsnlioA4cnZXFlmVsCQi2OOEGdCyfZJ7tveGnNU8P5kRK/3L0LJ8C', // polkilo
        'auth_key'   => 'auth_key_1',
        'created_at' => 1455353947,
        'updated_at' => 1455353947,
    ],
    $this::ID_USER_CONFIRMED_LOGIN => [
        'id'         => $this::ID_USER_CONFIRMED_LOGIN,
        'type'       => app\models\User::TYPE_USER,
        'login'      => $this->getLogin($this::ID_USER_CONFIRMED_LOGIN),
        'password'   => '$2y$13$XsnlioA4cnZXFlmVsCQi2OOEGdCyfZJ7tveGnNU8P5kRK/3L0LJ8C', // polkilo
        'auth_key'   => 'auth_key_confirmed_login',
        'created_at' => 1455353947,
        'updated_at' => 1455353947,
    ],
    $this::ID_USER_2 => [
        'id'         => $this::ID_USER_2,
        'type'       => app\models\User::TYPE_USER,
        'login'      => $this->getLogin($this::ID_USER_2),
        'password'   => '$2y$13$XsnlioA4cnZXFlmVsCQi2OOEGdCyfZJ7tveGnNU8P5kRK/3L0LJ8C', // polkilo
        'auth_key'   => 'auth_key_confirmed_login',
        'created_at' => 1455353947,
        'updated_at' => 1455353947,
    ],
];