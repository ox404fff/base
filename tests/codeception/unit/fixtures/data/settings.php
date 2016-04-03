<?
/**
 * @var test\fixtures\SettingsFixture $this
 */
use test\fixtures\UserFixture;
use \app\modules\cabinet\models\Settings;
use \app\base\helpers\DateTime;
use test\fixtures\SettingsFixture;

return [
    [
        'user_id'       => $this::USER_ID,
        'type'          => Settings::TYPE_CHANGE_EMAIL,
        'is_confirm'    => false,
        'json_data'     => \yii\helpers\Json::encode(['email' => 'unconfirmed_email1@example.com']),

        'created_at'    => DateTime::time(),
        'updated_at'    => DateTime::time(),
    ],
    [
        'user_id'       => $this::USER_ID,
        'type'          => Settings::TYPE_CHANGE_EMAIL,
        'is_confirm'    => false,
        'json_data'     => \yii\helpers\Json::encode(['email' => 'unconfirmed_email2@example.com']),

        'created_at'    => DateTime::time(),
        'updated_at'    => DateTime::time(),
    ],
    [
        'user_id'       => $this::USER_ID,
        'type'          => Settings::TYPE_CHANGE_EMAIL,
        'is_confirm'    => true,
        'json_data'     => \yii\helpers\Json::encode(['email' => 'confirmed_email1@example.com']),

        'created_at'    => DateTime::time(),
        'updated_at'    => DateTime::time(),
    ],
    [
        'user_id'       => $this::USER_ID,
        'type'          => Settings::TYPE_CHANGE_EMAIL,
        'is_confirm'    => true,
        'json_data'     => \yii\helpers\Json::encode(['email' => 'confirmed_email2@example.com']),

        'created_at'    => DateTime::time(),
        'updated_at'    => DateTime::time(),
    ],
    [
        'user_id'       => UserFixture::ID_USER_INACTIVE,
        'type'          => Settings::TYPE_CHANGE_EMAIL,
        'is_confirm'    => false,
        'json_data'     => \yii\helpers\Json::encode([
            'email' => UserFixture::getLogin(
                UserFixture::ID_USER_INACTIVE
            )
        ]),

        'created_at'    => DateTime::time(),
        'updated_at'    => DateTime::time(),
    ],
    [
        'user_id'       => UserFixture::ID_USER,
        'type'          => Settings::TYPE_CHANGE_EMAIL,
        'is_confirm'    => false,
        'json_data'     => \yii\helpers\Json::encode([
            'email' => 'new_changed_email@exemple.com'
        ]),

        'created_at'    => DateTime::time(),
        'updated_at'    => DateTime::time(),
    ],
    [
        'user_id'       => UserFixture::ID_USER_CONFIRMED_LOGIN,
        'type'          => Settings::TYPE_CHANGE_EMAIL,
        'is_confirm'    => false,
        'json_data'     => \yii\helpers\Json::encode([
            'email' => 'new_changed_email@exemple.com'
        ]),

        'created_at'    => DateTime::time(),
        'updated_at'    => DateTime::time(),
    ],

    [
        'user_id'       => UserFixture::ID_USER_CONFIRMED_LOGIN,
        'type'          => Settings::TYPE_CHANGE_EMAIL,
        'is_confirm'    => true,
        'json_data'     => \yii\helpers\Json::encode([
            'email' => 'confirmed_email@email.com'
        ]),

        'created_at'    => DateTime::time(),
        'updated_at'    => DateTime::time(),
    ],

    [
        'user_id'       => UserFixture::ID_USER_2,
        'type'          => SettingsFixture::TYPE_CHANGE,
        'is_confirm'    => true,
        'json_data'     => \yii\helpers\Json::encode([
            'email' => UserFixture::getLogin(UserFixture::ID_USER_2),
        ]),

        'created_at'    => DateTime::time(),
        'updated_at'    => DateTime::time(),
    ],
    [
        'user_id'       => UserFixture::ID_USER_2,
        'type'          => SettingsFixture::TYPE_CHANGE,
        'is_confirm'    => false,
        'json_data'     => \yii\helpers\Json::encode([
            'email' => 'changed_email_1@email.com',
        ]),

        'created_at'    => DateTime::time(),
        'updated_at'    => DateTime::time(),
    ],
    [
        'user_id'       => UserFixture::ID_USER_2,
        'type'          => SettingsFixture::TYPE_CHANGE,
        'is_confirm'    => false,
        'json_data'     => \yii\helpers\Json::encode([
            'email' => 'changed_email_2@email.com',
        ]),

        'created_at'    => DateTime::time(),
        'updated_at'    => DateTime::time(),
    ],
    [
        'user_id'       => SettingsFixture::OTHER_USER_ID_2,
        'type'          => SettingsFixture::TYPE_CHANGE,
        'is_confirm'    => false,
        'json_data'     => \yii\helpers\Json::encode([
            'email' => 'changed_email_2@email.com',
        ]),

        'created_at'    => DateTime::time() - DateTime::DAY * 2,
        'updated_at'    => DateTime::time() - DateTime::DAY * 2,
    ],

    /**
     * Data for Get All settings method test
     */
    [
        'user_id'       => $this::OTHER_USER_ID,
        'type'          => SettingsFixture::TYPE_CHANGE,
        'is_confirm'    => true,
        'json_data'     => \yii\helpers\Json::encode(['test' => 'test0']),

        'created_at'    => DateTime::time(),
        'updated_at'    => DateTime::time(),
    ],
    [
        'user_id'       => $this::OTHER_USER_ID,
        'type'          => SettingsFixture::TYPE_CHANGE,
        'is_confirm'    => false,
        'json_data'     => \yii\helpers\Json::encode(['test' => 'test1']),

        'created_at'    => DateTime::time(),
        'updated_at'    => DateTime::time(),
    ],
    [
        'user_id'       => $this::OTHER_USER_ID,
        'type'          => SettingsFixture::TYPE_CHANGE_2,
        'is_confirm'    => true,
        'json_data'     => \yii\helpers\Json::encode(['test' => 'test2']),

        'created_at'    => DateTime::time() - DateTime::DAY * 3,
        'updated_at'    => DateTime::time() - DateTime::DAY * 3,
    ],
    [
        'user_id'       => $this::OTHER_USER_ID,
        'type'          => SettingsFixture::TYPE_CHANGE_2,
        'is_confirm'    => false,
        'json_data'     => \yii\helpers\Json::encode(['test' => 'test3']),

        'created_at'    => DateTime::time() - DateTime::DAY * 2,
        'updated_at'    => DateTime::time() - DateTime::DAY * 2,
    ],
    [
        'user_id'       => $this::OTHER_USER_ID,
        'type'          => SettingsFixture::TYPE_CHANGE_3,
        'is_confirm'    => true,
        'json_data'     => \yii\helpers\Json::encode(['test' => 'test4']),

        'created_at'    => DateTime::time(),
        'updated_at'    => DateTime::time(),
    ],
    [
        'user_id'       => $this::OTHER_USER_ID,
        'type'          => SettingsFixture::TYPE_CHANGE_4,
        'is_confirm'    => false,
        'json_data'     => \yii\helpers\Json::encode(['test' => 'test5']),

        'created_at'    => DateTime::time(),
        'updated_at'    => DateTime::time(),
    ],
    [
        'user_id'       => $this::OTHER_USER_ID,
        'type'          => SettingsFixture::TYPE_CHANGE_5,
        'is_confirm'    => false,
        'json_data'     => \yii\helpers\Json::encode(['test' => 'test5.5']),

        'created_at'    => DateTime::time() - 60,
        'updated_at'    => DateTime::time() - 60,
    ],
    [
        'user_id'       => $this::OTHER_USER_ID,
        'type'          => SettingsFixture::TYPE_CHANGE_5,
        'is_confirm'    => true,
        'json_data'     => \yii\helpers\Json::encode(['test' => 'test6']),

        'created_at'    => DateTime::time(),
        'updated_at'    => DateTime::time(),
    ],
    [
        'user_id'       => $this::OTHER_USER_ID,
        'type'          => SettingsFixture::TYPE_CHANGE_6,
        'is_confirm'    => true,
        'json_data'     => \yii\helpers\Json::encode(['test' => 'test7']),

        'created_at'    => DateTime::time() - DateTime::DAY * 4,
        'updated_at'    => DateTime::time() - DateTime::DAY * 4,
    ],
    [
        'user_id'       => $this::OTHER_USER_ID,
        'type'          => SettingsFixture::TYPE_CHANGE_6,
        'is_confirm'    => false,
        'json_data'     => \yii\helpers\Json::encode(['test' => 'test8']),

        'created_at'    => DateTime::time() - DateTime::DAY / 2,
        'updated_at'    => DateTime::time() - DateTime::DAY / 2,
    ],
    [
        'user_id'       => $this::OTHER_USER_ID,
        'type'          => SettingsFixture::TYPE_CHANGE_6,
        'is_confirm'    => false,
        'json_data'     => \yii\helpers\Json::encode(['test' => 'test9']),

        'created_at'    => DateTime::time() - DateTime::DAY / 2,
        'updated_at'    => DateTime::time() - DateTime::DAY / 2,
    ],
    [
        'user_id'       => $this::OTHER_USER_ID,
        'type'          => SettingsFixture::TYPE_CHANGE_6,
        'is_confirm'    => false,
        'json_data'     => \yii\helpers\Json::encode(['test' => 'test10']),

        'created_at'    => DateTime::time(),
        'updated_at'    => DateTime::time(),
    ],
];