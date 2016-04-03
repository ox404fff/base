<?php

namespace test\fixtures;

use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{

    public $modelClass = 'app\models\User';

    /**
     * Admin user id
     */
    const ID_USER_ADMIN = 11;

    /**
     * Normal user id
     */
    const ID_USER = 12;

    /**
     * Normal deleted user id
     */
    const ID_USER_DELETED = 13;

    /**
     * Inactive user id
     */
    const ID_USER_INACTIVE = 14;

    /**
     * Inactive deleted user id
     */
    const ID_USER_INACTIVE_DELETED = 15;

    /**
     * Inactive user with admin login
     */
    const ID_USER_INACTIVE_ADMIN_DUPLICATE = 16;

    /**
     * User with confirmed login
     */
    const ID_USER_CONFIRMED_LOGIN = 17;

    /**
     * Inactive user id
     */
    const ID_USER_INACTIVE_2 = 18;

    /**
     * Normal user id
     */
    const ID_USER_2 = 19;

    /**
     * Not exists user
     */
    const ID_NOT_EXISTS_USER = -1;

    /**
     * @var array User passwords by types
     */
    private static $_passwords = [
        self::ID_USER_ADMIN             => 'polkilo',
        self::ID_USER                   => 'polkilo1',
        self::ID_USER_DELETED           => 'polkilo2',
        self::ID_USER_INACTIVE          => 'polkilo3',
        self::ID_USER_INACTIVE_DELETED  => 'polkilo4',
        self::ID_NOT_EXISTS_USER        => 'not_exists_password',
        self::ID_USER_2                 => 'polkilo',
    ];


    /**
     * @var array User logins by types
     */
    private static $_logins = [
        self::ID_USER_ADMIN             => '',
        self::ID_USER                   => 'user1@email.com',
        self::ID_USER_DELETED           => 'user2@email.com',
        self::ID_USER_INACTIVE          => 'new_user1@email.com',
        self::ID_USER_INACTIVE_DELETED  => 'new_user2@email.com',
        self::ID_NOT_EXISTS_USER        => 'not_exists_login@undef.nadomain',
        self::ID_USER_CONFIRMED_LOGIN   => 'confirmed_email@email.com',
        self::ID_USER_2                 => 'normal_user_1@email.com',
    ];


    /**
     * @var array User Names by types
     */
    private static $_names = [
        self::ID_USER_ADMIN             => '',
        self::ID_USER                   => 'user1@email.com',
        self::ID_USER_DELETED           => 'user2@email.com',
        self::ID_USER_INACTIVE          => 'new_user1@email.com',
        self::ID_USER_INACTIVE_DELETED  => 'new_user2@email.com',
        self::ID_NOT_EXISTS_USER        => 'not_exists_login@undef.nadomain',
        self::ID_USER_CONFIRMED_LOGIN   => 'confirmed_email@email.com',
        self::ID_USER_2                 => 'normal_user_1@email.com',
    ];

    /**
     * @var array User auth keys by types
     */
    private static $_auth_keys = [
        self::ID_USER_ADMIN             => 'auth_key_1',
        self::ID_USER                   => 'auth_key_2',
        self::ID_USER_DELETED           => 'auth_key_3',
        self::ID_USER_INACTIVE          => 'auth_key_4',
        self::ID_USER_INACTIVE_DELETED  => 'auth_key_5',
        self::ID_NOT_EXISTS_USER        => 'not_exists_auth_key',
        self::ID_USER_2                 => 'auth_key_6',
    ];

    /**
     * @var array User auth keys by types
     */
    private static $_roles = [
        self::ID_USER_ADMIN             => 'admin',
        self::ID_USER                   => 'user',
        self::ID_USER_DELETED           => 'user',
        self::ID_USER_INACTIVE          => null,
        self::ID_USER_INACTIVE_DELETED  => null,
        self::ID_NOT_EXISTS_USER        => null,
        self::ID_USER_2                 => 'user',
    ];


    public function load()
    {
        parent::load();
    }


    /**
     * Get password by type
     *
     * @param $userType
     * @return bool
     */
    public static function getRole($userType)
    {
        return isset(self::$_roles[$userType]) ?
            self::$_roles[$userType] : false;
    }


    /**
     * Get password by type
     *
     * @param $userType
     * @param $hash
     *
     * @return bool
     */
    public static function getPassword($userType, $hash = false)
    {
        if (!isset(self::$_passwords[$userType])) {
            return false;
        }

        if ($hash) {
            return \Yii::$app->getSecurity()->generatePasswordHash(self::$_passwords[$userType]);
        }

        return self::$_passwords[$userType];
    }


    /**
     * Get password by type
     *
     * @param $userType
     * @return bool
     */
    public static function getAuthKey($userType)
    {
        return isset(self::$_auth_keys[$userType]) ?
            self::$_auth_keys[$userType] : false;
    }


    /**
     * Get login by type
     *
     * @param $userType
     * @return string|bool
     */
    public static function getLogin($userType)
    {
        if ($userType == self::ID_USER_ADMIN) {
            return \Yii::$app->params['adminEmail'];
        }

        return isset(self::$_logins[$userType]) ?
            self::$_logins[$userType] : false;
    }


    /**
     * Get user name by type
     *
     * @param $userType
     * @return string|bool
     */
    public static function getName($userType)
    {
        return isset(self::$_names[$userType]) ?
            self::$_names[$userType] : false;
    }
}