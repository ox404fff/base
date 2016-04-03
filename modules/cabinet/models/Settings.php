<?php

namespace app\modules\cabinet\models;

use app\base\behaviors\JsonDataBehavior;
use app\base\behaviors\LifeTimeBehavior;
use app\base\db\ActiveQuery;
use app\base\db\ActiveRecord;
use app\base\helpers\DateTime;
use app\models\User;
use app\modules\auth\components\events\ForgotEvent;
use app\modules\auth\components\events\RegistrationEvent;
use app\modules\auth\components\events\SetPasswordEvent;
use app\modules\cabinet\components\events\ChangeEmailEvent;
use app\modules\cabinet\components\events\ChangePasswordEvent;
use app\modules\cabinet\components\events\ConfirmEmailEvent;
use yii\base\Exception;
use yii\helpers\ArrayHelper;


/**
 * Class Settings
 *
 * @property int       $id               pk
 * @property int       $user_id          User identity
 * @property int       $type             Type changed setting
 * @property string    $json_data        Changed attributes
 * @property bool      $is_confirm       Confirmed change?
 *
 * @property User    $user     User relation
 *
 * @package app\modules\cabinet\models
 *
 * @mixin JsonDataBehavior|LifeTimeBehavior
 */
class Settings extends ActiveRecord
{

    /**
     * Change user email
     */
    const TYPE_CHANGE_EMAIL = 1;


    /**
     * Reset user password
     */
    const TYPE_RESET_PASSWORD = 2;


    /**
     * Change user password
     */
    const TYPE_CHANGE_PASSWORD = 3;


    /**
     * @return string the name of the table associated with this ActiveRecord class.
     */
    public static function tableName()
    {
        return 'settings';
    }


    /**
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'changed_attributes' => [
                'class' => JsonDataBehavior::className(),
            ],
            'life_time' => [
                'class'              => LifeTimeBehavior::className(),
                'createdAtAttribute' => self::ATTRIBUTE_CREATED_AT,
                'lifeTimesTypes'     => self::$lifeTimeByTypes
            ]
        ]);
    }


    /**
     * Map, type settings change and lifetime, in seconds
     *
     * @var array
     */
    public static $lifeTimeByTypes = [
        self::TYPE_CHANGE_EMAIL   => DateTime::HOUR,
        self::TYPE_RESET_PASSWORD => DateTime::HOUR
    ];


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
     * Create new change settings record
     *
     * @param $userId
     * @param $type
     * @param $attributes
     * @param bool $isConfirm
     *
     * @return Settings
     */
    public static function create($userId, $type, $attributes = [], $isConfirm = true)
    {
        $model = new Settings();
        $model->user_id = $userId;
        $model->type = $type;
        $model->setJsonAttributes($attributes);
        $model->is_confirm = $isConfirm;

        return $model;
    }


    /**
     * Get last change settings record
     *
     * @param $userId
     * @param $type
     * @param $isConfirm
     *
     * @return Settings
     */
    public static function getLast($userId, $type, $isConfirm = null)
    {
        $condition = self::_getBaseQuery($userId, $type);

        if (!is_null($isConfirm)) {
            $condition->andWhere(['is_confirm' => $isConfirm]);
        }

        $condition->orderBy(['id' => SORT_DESC]);

        return $condition->one();
    }


    /**
     * Get last ids change settings, grouped by type
     *
     * @param $userId
     * @param null $isConfirm
     * @return array[type] = id
     */
    public static function getLastAll($userId, $isConfirm = null)
    {
        $condition = self::_getBaseQuery($userId);
        $condition->select('MAX(id) as id, type');
        $condition->groupBy('type');
        $condition->indexBy('type');
        $condition->orderBy(['id' => SORT_DESC]);

        if (!is_null($isConfirm)) {
            $condition->andWhere(['is_confirm' => $isConfirm]);
        }

        return $condition->column();
    }


    /**
     * Get all user settings by ids
     *
     * @param $userId
     * @param $ids
     * @return Settings[]
     */
    public static function getByIds($userId, $ids)
    {
        $condition = self::_getBaseQuery($userId)
            ->andWhere(['id' => $ids])
            ->orderBy(['id' => SORT_DESC])
            ->indexBy('id');

        return $condition->all();
    }


    /**
     * Get all user settings
     *
     * @param $userId
     * @param null $isConfirmed
     * @return Settings[]
     */
    public static function getAll($userId, $isConfirmed = null)
    {
        $result = [];

        if ($isConfirmed === true || is_null($isConfirmed)) {

            self::_appendLastAll($userId, true, $result);

            if ($isConfirmed === true) {
                return $result;
            }

        }


        if ($isConfirmed === false || is_null($isConfirmed)) {

            self::_appendLastAll($userId, false, $result);

            if ($isConfirmed === false) {
                return $result;
            }

        }

        return $result;
    }


    /**
     * Append last settings in array, selected by confirmation type
     *
     * @param $userId
     * @param $confirmed
     * @param array $result
     * @return array
     */
    public static function _appendLastAll($userId, $confirmed, &$result = [])
    {
        $lastIds = self::getLastAll($userId, $confirmed);
        $lastModels = self::getByIds($userId, $lastIds);

        foreach ($lastModels as $id => $model) {

            if (!$model->is_confirm && $model->isExpired()) {
                continue;
            }

            if (isset($result[$model->type]) && $result[$model->type]->id > $model->id) {
                continue;
            }

            $result[$model->type] = $model;

        }

        return $result;
    }



    /**
     * Return true if last setting is confirmed
     *
     * @param $userId
     * @param $type
     *
     * @return bool|null (return null if last change is not confirm and time is over)
     */
    public static function isConfirmedLast($userId, $type)
    {
        $lastChange = self::getLast($userId, $type);

        if (empty($lastChange) || !$lastChange->is_confirm && $lastChange->isExpired()) {
            return null;
        }
        /**
         * @var Settings $lastChange
         */
        return !empty($lastChange) && $lastChange->is_confirm;
    }


    /**
     * Cancel settings change
     *
     * @param $userId
     * @param $type
     * @return bool|null
     */
    public static function rollbackSettingsChanges($userId, $type)
    {

        $lastConfirmed = self::getLast($userId, $type, true);
        if (empty($lastConfirmed)) {
            return false;
        }

        $last = self::getLast($userId, $type);
        if ($last->is_confirm) {
            return null;
        }

        $confirmedSetting = self::create($userId, $type, $lastConfirmed->getJsonAttributes(), true);

        return $confirmedSetting->save();

    }


    /**
     * Return true if exist confirmed settings
     *
     * @param $userId
     * @param $type
     *
     * @return bool
     */
    public static function isExistConfirmedSettings($userId, $type)
    {
        $lastConfirmed = self::getLast($userId, $type, true);
        return !empty($lastConfirmed);
    }


    /**
     * if life time's end
     *
     * @return mixed
     */
    public function isExpired()
    {
        return $this->getBehavior('life_time')->isExpired($this->type);
    }


    /**
     * @param $userId
     * @param $type
     * @return ActiveQuery
     */
    private static function _getBaseQuery($userId, $type = null)
    {
        $condition = self::find()
            ->where(['user_id' => $userId]);

        if (!is_null($type)) {
            $condition->andWhere(['type' => $type]);
        }

        return $condition;
    }


    /**
     * Create setting email record, when new user registration
     *
     * @param RegistrationEvent $event
     * @throws Exception
     */
    public static function onRegistration(RegistrationEvent $event)
    {
        /**
         * Set first email change settings
         * (To be compatible with the functionality of the email address change)
         */
        $changeEmailSetting = Settings::create($event->user->id, Settings::TYPE_CHANGE_EMAIL, [
            'email' => $event->user->getEmail()
        ], false);

        if (!$changeEmailSetting->save()) {
            throw new Exception(\Yii::t('app', 'Registration is temporarily unavailable'));
        }


        /**
         * Set first password change settings
         * (To be compatible with the functionality of the password change)
         */
        $changePasswordSetting = Settings::create($event->user->id, Settings::TYPE_CHANGE_PASSWORD, [
        ], true);

        if (!$changePasswordSetting->save()) {
            throw new Exception(\Yii::t('app', 'Registration is temporarily unavailable'));
        }
    }


    /**
     * Create setting reset password record, when user send reset password code
     *
     * @param ForgotEvent $event
     * @throws Exception
     */
    public static function onSendResetPasswordCode(ForgotEvent $event)
    {
        $resetPasswordSetting = Settings::create($event->user->id, Settings::TYPE_RESET_PASSWORD, [
            'confirm_code_id' => $event->confirmCode->id
        ], false);

        if (!$resetPasswordSetting->save()) {
            throw new Exception('Service password recovering, is temporarily unavailable');
        }
    }


    /**
     * Create settings change password on successfully reset password
     *
     * @param SetPasswordEvent $event
     * @throws Exception
     */
    public static function onResetPassword(SetPasswordEvent $event)
    {
        if (!$event->confirmCode->delete()) {
            throw new Exception('Service password recovering, is temporarily unavailable');
        }

        $event->settingChange->is_confirm = true;
        if (!$event->settingChange->save()) {
            throw new Exception('Service password recovering, is temporarily unavailable');
        }

        $passwordChangeSettings = Settings::create($event->user->id, Settings::TYPE_CHANGE_PASSWORD, [], true);
        if (!$passwordChangeSettings->save()) {
            throw new Exception('Service password recovering, is temporarily unavailable');
        }
    }


    /**
     * Create change email record
     *
     * @param ChangeEmailEvent $event
     * @throws Exception
     */
    public static function onSendConfirmEmail(ChangeEmailEvent $event)
    {
        $changeEmailSetting = Settings::create($event->user->id, Settings::TYPE_CHANGE_EMAIL, [
            'email' => $event->email
        ], false);

        if (!$changeEmailSetting->save()) {
            throw new Exception(\Yii::t('app', 'Change email settings is temporarily unavailable'));
        }
    }


    /**
     * On change password
     *
     * @param ChangePasswordEvent $event
     * @throws Exception
     */
    public static function onChangePassword(ChangePasswordEvent $event)
    {
        $settingsChange = Settings::create($event->user->id, Settings::TYPE_CHANGE_PASSWORD);

        if (!$settingsChange->save()) {
            throw new Exception('Settings is temporarily unavailable');
        }
    }


    /**
     * Activate user if not active
     *
     * @param ConfirmEmailEvent $event
     * @throws Exception
     */
    public static function onConfirmEmail(ConfirmEmailEvent $event)
    {
        $event->settingsChange->is_confirm = true;
        if (!$event->settingsChange->save()) {
            throw new Exception('Email confirmation is temporarily unavailable');
        }
    }


}
