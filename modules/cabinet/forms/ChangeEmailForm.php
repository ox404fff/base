<?php

namespace app\modules\cabinet\forms;

use app\base\behaviors\FormBehavior;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use app\modules\cabinet\components\events\ChangeEmailEvent;
use app\modules\cabinet\models\Settings;

/**
 * Changing email settings
 *
 * Class ChangeEmailForm
 * @package app\modules\cabinet\forms
 *
 * @mixin FormBehavior
 */
class ChangeEmailForm extends BaseEmailForm
{

    /**
     * @var null is need confirm email static cache
     */
    private $_isNeedConfirmEmail = null;

    /**
     * Event when begin procedure change email address
     */
    const EVENT_CONFIRM_EMAIL_CODE = 'confirm_email_code';


    /**
     * @return array the behaviors.
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'form' => [
                'class'  => FormBehavior::className(),
                'events' => [
                    self::EVENT_CONFIRM_EMAIL_CODE => [
                        ['app\models\ConfirmCode', 'onSendConfirmEmail'],
                        ['app\modules\cabinet\models\Settings', 'onSendConfirmEmail'],
                        ['app\services\ConfirmCodeService', 'onSendConfirmEmail'],
                    ],
                ]
            ]
        ]);
    }


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [

        ]);
    }


    /**
     * @return array the attribute labels
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
        ]);
    }


    /**
     * @return bool
     */
    public function beforeValidate()
    {
        return parent::beforeValidate();
    }


    /**
     * Return is need confirmed email address
     *
     * @return bool
     */
    public function getIsNotEqualsOldEmail()
    {
        if (is_null($this->_isNeedConfirmEmail)) {

            $user = $this->getUser();

            $lastSettingConfirmed = Settings::getLast($user->id, Settings::TYPE_CHANGE_EMAIL, true);

            $this->_isNeedConfirmEmail = (bool) (empty($lastSettingConfirmed) || $lastSettingConfirmed->getJsonAttribute('email') != $this->email);
        }

        return $this->_isNeedConfirmEmail;
    }



    /**
     * Create change email settings
     *
     * @return bool
     * @throws Exception
     */
    public function doChange()
    {
        if ($this->validate()) {

            $user = $this->getUser();

            if ($this->getIsNotEqualsOldEmail()) {

                $event = new ChangeEmailEvent();
                $event->user = $user;
                $event->email = $this->email;
                $this->trigger(self::EVENT_CONFIRM_EMAIL_CODE, $event);

            } else {

                $rollbackResult = Settings::rollbackSettingsChanges($user->id, Settings::TYPE_CHANGE_EMAIL);
                if ($rollbackResult === false) {
                    throw new Exception(\Yii::t('app', 'Change email settings is temporarily unavailable'));
                }
            }

            return true;
        }

        return false;
    }



}
