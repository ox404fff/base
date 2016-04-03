<?php

namespace app\models;

use app\modules\cabinet\components\events\ConfirmEmailEvent;
use app\base\behaviors\StaticCacheBehavior;
use app\base\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\base\Exception;


/**
 * Class User
 *
 * @property int       $id               pk
 * @property string    $login            login (email)
 * @property string    $password         password hash
 *
 * @property string    $auth_key         for generate auto login cookie
 *
 * @property int       $type             user type (self::TYPE_)
 *
 * @package app\modules\auth\models
 *
 * @mixin StaticCacheBehavior
 */
class User extends ActiveRecord implements IdentityInterface
{

    /**
     * New user (With Unconfirmed login)
     */
    const TYPE_INACTIVE = 0;

    /**
     * Regular user
     */
    const TYPE_USER = 1;

    /**
     * Regular user
     */
    const TYPE_USER_ADMIN = 2;

    /**
     * @var array Active user types
     */
    public static $activeUserTypes = [
        self::TYPE_USER, self::TYPE_USER_ADMIN
    ];

    /**
     * @var array Admin user types
     */
    public static $adminUserTypes = [
        self::TYPE_USER_ADMIN
    ];


    /**
     * @var string Administrator e-mail address
     */
    public $administratorEmail;

    /**
     * @return string the name of the table associated with this ActiveRecord class.
     */
    public static function tableName()
    {
        return 'users';
    }



    /**
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'staticCache' => [
                'class'   => StaticCacheBehavior::className(),
            ]
        ]);
    }


    /**
     * Return array active user types
     *
     * @return array
     */
    public static function getActiveUserTypes()
    {
        return self::$activeUserTypes;
    }


    /**
     * @inheritdoc
     * @return static
     */
    public static function findIdentity($id)
    {
        return self::singleton()->staticCache(function() use ($id) {

            return self::find()->where(['id' => $id])->one();

        }, ['findById', $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * Finds user by username
     *
     * @param  string $login
     * @param $type
     *
     * @return User|null
     */
    public static function findByLogin($login, $type = null)
    {
        $condition = self::find()->where(['login' => $login]);

        if (!is_null($type)) {

            $condition->andWhere(['type' => $type]);
            $condition->orderBy(['id' => SORT_DESC]);
        } else {

            $condition->orderBy(['type' => SORT_DESC, 'id' => SORT_DESC]);
        }

        return $condition->one();
    }


    /**
     * Check is exists user
     *
     * @param string $login
     * @param bool|array $type
     *
     * @return bool
     */
    public static function isLoginExists($login, $type = null)
    {
        $condition = self::find()->where(['login' => $login]);

        if (!is_null($type)) {
            $condition->andWhere(['type' => $type]);
        }

        return $condition->exists();

    }


    /**
     * Create new user
     *
     * @param $login
     * @param $passwordHash
     * @param $type
     *
     * @return self
     */
    public static function createUser($login, $passwordHash, $type = self::TYPE_INACTIVE)
    {
        $user = new User();

        $user->type     = $type;
        $user->login    = $login;
        $user->password = $passwordHash;

        $user->auth_key = \Yii::$app->security->generateRandomString(32);

        return $user;
    }


    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Return user login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }


    /**
     * Return user email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->login;
    }


    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Is active user
     *
     * @return bool
     */
    public function isActive()
    {
        if ($this->isDeleted()) {
            return false;
        }

        if (\Yii::$app->getAuthManager()->checkAccess($this->id, 'user')) {
            return true;
        }

        if (\Yii::$app->getAuthManager()->checkAccess($this->id, 'admin')) {
            return true;
        }

        return false;
    }


    /**
     * Is admin user
     *
     * @return bool
     */
    public function isAdmin()
    {
        return \Yii::$app->getAuthManager()->checkAccess($this->id, 'admin') && !$this->isDeleted();
    }


    /**
     * Is set password for user
     *
     * @return bool
     */
    public function isPasswordSet()
    {
        return !empty($this->password);
    }


    /**
     * Activate user if not active
     *
     * @param ConfirmEmailEvent $event
     * @throws Exception
     */
    public static function onConfirmEmail(ConfirmEmailEvent $event)
    {
        if (!$event->user->isActive()) {
            $event->user->type = User::TYPE_USER;

            if (!$event->user->save()) {
                throw new Exception('Email confirmation is temporarily unavailable');
            }
        }
    }

}
