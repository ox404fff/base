<?
use test\fixtures\ConfirmCodeFixture;

/**
 * @var test\fixtures\ConfirmCodeFixture $this
 */

return [
    ConfirmCodeFixture::ID_1 => [
        'user_id'      => $this::USER_ID,
        'type'         => $this::TYPE,
        'confirm_code' => $this::CODE_ONE_USER,

        'created_at' => \app\base\helpers\DateTime::time(),
        'updated_at' => \app\base\helpers\DateTime::time(),
    ],
    ConfirmCodeFixture::ID_2 => [
        'type'         => $this::TYPE,
        'confirm_code' => $this::CODE_ALL_USERS,

        'created_at' => \app\base\helpers\DateTime::time(),
        'updated_at' => \app\base\helpers\DateTime::time(),
    ],

    ConfirmCodeFixture::ID_3 => [
        'type'         => \app\models\ConfirmCode::TYPE_CONFIRM_EMAIL,
        'user_id'      => test\fixtures\UserFixture::ID_USER_INACTIVE,
        'confirm_code' => $this::createConfirmCode(test\fixtures\UserFixture::ID_USER_INACTIVE),

        'created_at' => \app\base\helpers\DateTime::time(),
        'updated_at' => \app\base\helpers\DateTime::time(),
    ],

    ConfirmCodeFixture::ID_4 => [
        'type'         => \app\models\ConfirmCode::TYPE_CONFIRM_EMAIL,
        'user_id'      => test\fixtures\UserFixture::ID_USER,
        'confirm_code' => $this::createConfirmCode(test\fixtures\UserFixture::ID_USER),

        'created_at' => \app\base\helpers\DateTime::time(),
        'updated_at' => \app\base\helpers\DateTime::time(),
    ],

    ConfirmCodeFixture::ID_5 => [
        'type'         => \app\models\ConfirmCode::TYPE_CONFIRM_EMAIL,
        'user_id'      => test\fixtures\UserFixture::ID_USER_CONFIRMED_LOGIN,
        'confirm_code' => $this::createConfirmCode(test\fixtures\UserFixture::ID_USER_CONFIRMED_LOGIN),
        'is_deleted'    => true,

        'created_at' => \app\base\helpers\DateTime::time(),
        'updated_at' => \app\base\helpers\DateTime::time(),
    ],
    ConfirmCodeFixture::ID_6 => [
        'type'         => \app\models\ConfirmCode::TYPE_RESET_PASSWORD_EMAIL,
        'user_id'      => test\fixtures\UserFixture::ID_USER,
        'confirm_code' => $this::createConfirmCode(test\fixtures\ConfirmCodeFixture::USER_ID),

        'created_at' => \app\base\helpers\DateTime::time(),
        'updated_at' => \app\base\helpers\DateTime::time(),
    ],
];