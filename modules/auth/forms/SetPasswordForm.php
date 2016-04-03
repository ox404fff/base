<?php

namespace app\modules\auth\forms;

use app\base\behaviors\FormBehavior;
use app\base\web\ModelForm;
use app\models\ConfirmCode;
use app\models\User;
use app\modules\auth\components\events\SetPasswordEvent;
use app\modules\cabinet\models\Settings;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Recover password with sending verification code to email
 *
 * @mixin FormBehavior
 */
class SetPasswordForm extends ModelForm
{

    /**
     * @var string Code sent to email, for changing password
     */
    public $code;

    /**
     * @var string New password
     */
    public $password;

    /**
     * @var null|ConfirmCode Changing password code model
     */
    public $confirmCode = null;

    /**
     * @var null|Settings Settings change record
     */
    public $settingsChange = null;


    /**
     * Event, when user reset password
     */
    const EVENT_PASSWORD_RESET_SUCCESS = 'password_reset_success';


    /**
     * @return array the behaviors.
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'form' => [
                'class'  => FormBehavior::className(),
                'events' => [
                    self::EVENT_PASSWORD_RESET_SUCCESS => [
                        ['\app\modules\cabinet\models\Settings', 'onResetPassword'],
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
        return [
            [['code', 'password'], 'required'],


            ['code', 'filter', 'filter' => 'trim'],
            ['code', 'filter', 'filter' => 'strtolower'],
            ['code', 'string', 'length' => ConfirmCode::getLength(ConfirmCode::TYPE_CONFIRM_EMAIL)],

            ['code', 'validateCode'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'login'        => \Yii::t('app', 'E-mail'),
            'sendPassword' => \Yii::t('app', 'Send password to email')
        ];
    }


    public function initByCode()
    {
        $this->confirmCode = ConfirmCode::findByCode(ConfirmCode::TYPE_RESET_PASSWORD_EMAIL, $this->code);

        if (!empty($this->confirmCode)) {

            $this->settingsChange = Settings::getLast($this->confirmCode->user_id, Settings::TYPE_RESET_PASSWORD, false);

            $this->setUser(User::findIdentity($this->confirmCode->user_id));

        }

    }


    /**
     * Validate code reset password
     */
    public function validateCode($attribute)
    {
        if (is_null($this->getUser())) {
            $this->addError($attribute, 'User is not found');
            return false;
        }
        if (empty($this->confirmCode)) {
            $this->addError($attribute, 'Password reset code is invalid');
            return false;
        }
        if (empty($this->settingsChange)) {
            $this->addError($attribute, 'The change request is outdated');
            return false;
        }
        if ($this->settingsChange->getJsonAttribute('confirm_code_id') != $this->confirmCode->id) {
            $this->addError($attribute, 'The change request is broken');
            return false;
        }
        return true;
    }


    /**
     * Setting new password for user, using the reset code
     */
    public function doSetPassword()
    {
        if ($this->validate()) {

            $this->getUser()->password = \Yii::$app->getSecurity()->generatePasswordHash($this->password);

            if (!$this->getUser()->save()) {
                throw new Exception('Service password recovering, is temporarily unavailable');
            }

            $event = new SetPasswordEvent();
            $event->user = $this->getUser();
            $event->settingChange = $this->settingsChange;
            $event->confirmCode = $this->confirmCode;
            $this->trigger(self::EVENT_PASSWORD_RESET_SUCCESS, $event);

            return true;
        }

        return false;
    }

}
