<?php

namespace app\models;

use app\base\behaviors\LifeTimeBehavior;
use app\base\db\ActiveRecord;
use app\base\helpers\DateTime;
use app\modules\cabinet\components\events\ChangeEmailEvent;
use app\modules\cabinet\components\events\ConfirmEmailEvent;
use yii\helpers\ArrayHelper;


/**
 * Class User
 *
 * @property int       $id               pk
 * @property int       $user_id          User ID
 * @property int       $type             Code type
 * @property string    $confirm_code     Any confirmation code
 *
 * @property User    $user     User relation
 *
 * @package app\models
 *
 * @mixin LifeTimeBehavior
 */
class ConfirmCode extends ActiveRecord
{

    /**
     * Code, for confirm email address
     */
    const TYPE_CONFIRM_EMAIL = 1;

    /**
     * Code, for reset password, sent by email
     */
    const TYPE_RESET_PASSWORD_EMAIL = 2;

    /**
     * @var array Confirm codes length
     */
    protected static $codesLength = [
        self::TYPE_CONFIRM_EMAIL => 32,
        self::TYPE_RESET_PASSWORD_EMAIL => 32,
    ];


    /**
     * Map, type confirmation codes and lifetime, in seconds
     *
     * @var array
     */
    public static $lifeTimeByTypes = [
        self::TYPE_CONFIRM_EMAIL        => DateTime::HOUR,
        self::TYPE_RESET_PASSWORD_EMAIL => DateTime::HOUR
    ];


    /**
     * Return length confirm code by type
     *
     * @param $type
     * @return null
     */
    public static function getLength($type)
    {
        return isset(self::$codesLength[$type]) ? self::$codesLength[$type] : null;
    }


    /**
     * @return string the name of the table associated with this ActiveRecord class.
     */
    public static function tableName()
    {
        return 'confirm_codes';
    }


    /**
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class'              => LifeTimeBehavior::className(),
                'createdAtAttribute' => self::ATTRIBUTE_CREATED_AT,
                'lifeTimesTypes'     => self::$lifeTimeByTypes
            ]
        ]);
    }


    /**
     * User relation
     *
     * @return User|null
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }


    /**
     * Create new confirmation code instance
     *
     * @param $userId
     * @param $type
     * @param $code
     * @return ConfirmCode
     */
    public static function createCode($type, $code, $userId = null)
    {
        $confirmCode = new ConfirmCode();
        $confirmCode->user_id = $userId;
        $confirmCode->type = $type;
        $confirmCode->confirm_code = $code;

        return $confirmCode;
    }


    /**
     * Check is correct confirm code
     *
     * @param $userId
     * @param $type
     * @param $code
     *
     * @return bool
     */
    public static function checkCode($type, $code, $userId = null)
    {
        $condition = self::_createBaseCondition($type, $userId);

        if (empty($condition)) {
            return false;
        }

        $condition->andWhere(['confirm_code' => $code]);

        $confirmCode = $condition->one();
        /**
         * @var self $confirmCode
         */
        if (empty($confirmCode)) {
            return false;
        }

        if ($userId !== $confirmCode->user_id && !is_null($confirmCode->user_id)) {
            return false;
        }


        return true;
    }


    /**
     * Get confirm code model by code string
     *
     * @param $type
     * @param $code
     * @param null $userId
     * @return bool|null|ConfirmCode
     */
    public static function findByCode($type, $code, $userId = null)
    {
        $condition = self::_createBaseCondition($type, $userId);

        if (empty($condition)) {
            return false;
        }

        $condition->andWhere(['confirm_code' => $code]);

        return $condition->one();
    }


    /**
     * Return true if exists valid confirm code for user
     *
     * @param $type
     * @param null $userId
     *
     * @return bool
     */
    public static function isCodeExists($type, $userId = null)
    {
        return self::_createBaseCondition($type, $userId)->exists();
    }


    /**
     * Find last created confirm code
     *
     * @param $type
     * @param null $userId
     * @return ConfirmCode
     */
    public static function getLastCode($type, $userId = null)
    {
        $condition = self::_createBaseCondition($type, $userId);

        $condition->orderBy(['id' => SORT_DESC]);

        return $condition->one();
    }


    /**
     * Delete confirm codes
     *
     * @param $type
     * @param null $userId
     *
     * @return int
     */
    public static function clearCodes($type, $userId = null)
    {
        $countDeleted = self::deleteAll(['type' => $type, 'user_id' => $userId]);

        return $countDeleted;
    }


    /**
     * Create base search confirm codes criteria
     *
     * @param $type
     * @param $userId
     * @return \app\base\db\ActiveQuery|bool
     */
    private static function _createBaseCondition($type, $userId)
    {
        $condition = self::find()->where(['type' => $type]);

        if (!isset(self::$lifeTimeByTypes[$type])) {
            return false;
        }

        $lifeTime = self::$lifeTimeByTypes[$type];

        if (!is_null($userId)) {
            $condition->andWhere('user_id = :user_id OR user_id IS NULL', [':user_id' => $userId]);
        }

        if ($lifeTime) {
             $condition->andWhere('created_at >= :die_time', [
                ':die_time' => DateTime::time() - $lifeTime
             ]);
        }

        return $condition;
    }


}
