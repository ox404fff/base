<?php

namespace app\modules\cabinet\forms;

use app\base\behaviors\FormBehavior;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use app\models\ConfirmCode;
use app\modules\cabinet\models\Settings;
use app\modules\cabinet\components\events\ConfirmEmailEvent;

/**
 * The model behind the confirmation email form.
 *
 * @mixin FormBehavior
 */
class ConfirmEmailForm extends BaseEmailForm
{


    /**
     * @var string - confirm code
     */
    public $code;

    /**
     * On change password
     */
    const EVENT_CHANGE_EMAIL = 'change_email';


    /**
     * @return array the behaviors.
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'form' => [
                'class'  => FormBehavior::className(),
                'events' => [
                    self::EVENT_CHANGE_EMAIL => [
                        ['\app\models\User', 'onConfirmEmail'],
                        ['\app\modules\cabinet\models\Settings', 'onConfirmEmail'],
                        ['\app\models\ConfirmCode', 'onConfirmEmail'],
                    ],
                ]
            ]
        ]);
    }


    /**
     * @return array the attribute labels
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'code'       => \Yii::t('app', 'Code confirm'),
        ]);
    }


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['code', 'required'],
            ['code', 'filter', 'filter' => 'trim'],
            ['code', 'filter', 'filter' => 'strtolower'],
            ['code', 'string', 'length' => ConfirmCode::getLength(ConfirmCode::TYPE_CONFIRM_EMAIL)],

            ['email', 'validateEmail'],
            ['code', 'validateCode'],
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
     * Check is email not already confirmed
     *
     * @param $attribute
     */
    public function validateEmail($attribute)
    {
        if ($this->isEmailConfirmed()) {
            $this->addError($attribute, \Yii::t('app', 'Email is already confirmed'));
        }
    }


    /**
     * Check is valid confirm code
     *
     * @param $attribute
     */
    public function validateCode($attribute)
    {
        if (!ConfirmCode::checkCode(ConfirmCode::TYPE_CONFIRM_EMAIL, $this->code, $this->user->getId())) {
            $this->addError($attribute, \Yii::t('app', 'Invalid confirmation code'));
        }
    }


    /**
     * Return true if exists confirm email
     *
     * @return bool
     */
    public function getIfExistConfirmedEmail()
    {
        $lastConfirmedEmail = Settings::getLast($this->user->id, Settings::TYPE_CHANGE_EMAIL, true);

        return !empty($lastConfirmedEmail);
    }



    /**
     * Set user email
     *
     * @throws Exception
     * @return bool
     */
    public function doConfirm()
    {
        if ($this->validate()) {
            $email = $this->settingsChange->getJsonAttribute('email');

            $this->user->login = $email;

            if (!$this->user->save()) {
                throw new Exception('Email confirmation is temporarily unavailable');
            }

            $event = new ConfirmEmailEvent();
            $event->user = $this->getUser();
            $event->settingsChange = $this->settingsChange;
            $event->email = $email;
            $this->trigger(self::EVENT_CHANGE_EMAIL, $event);

            return true;
        }

        return false;
    }

}
