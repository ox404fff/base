<?php

namespace app\modules\auth\forms;

use app\base\behaviors\FormBehavior;
use app\base\web\ModelForm;
use app\models\ConfirmCode;
use app\models\User;
use app\modules\auth\components\events\ForgotEvent;
use app\services\ConfirmCodeService;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Recover password with sending verification code to email
 *
 * @mixin FormBehavior
 */
class ForgotForm extends ModelForm
{
    /**
     * @var String User Login
     */
    public $login;

    /**
     * @var User Static cache with user model
     */
    private $_user = false;


    /**
     * Event, when user reset password
     */
    const EVENT_PASSWORD_RESET_CODE = 'password_reset_code';


    /**
     * @return array the behaviors.
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'form' => [
                'class'  => FormBehavior::className(),
                'events' => [
                    self::EVENT_PASSWORD_RESET_CODE => [
                        ['\app\modules\cabinet\models\Settings', 'onSendResetPasswordCode'],
                        ['\app\services\ConfirmCodeService', 'onSendResetPasswordCode'],
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
            [['login'], 'required'],

            ['login', 'string', 'length' => [6, 128]],
            ['login', 'app\modules\auth\components\validators\LoginValidator'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'login'    => \Yii::t('app', 'E-mail'),
        ];
    }

    /**
     * Check is exist user with login attribute
     *
     * @return bool
     */
    public function getIsUserExists()
    {
        $user = $this->getUser();
        return !is_null($user);
    }


    /**
     * Send reset password code, by email
     *
     * @throws Exception when can not save new user record
     * @return bool
     */
    public function doSendResetCode()
    {
        if ($this->validate()) {

            $user = $this->getUser();

            if (!empty($user)) {
                $resetPasswordCodeModel = ConfirmCodeService::createConfirmCode(ConfirmCode::TYPE_RESET_PASSWORD_EMAIL, $user->id);

                if (!$resetPasswordCodeModel->save()) {
                    throw new Exception('Service password recovering, is temporarily unavailable');
                }

                $event = new ForgotEvent();
                $event->user = $user;
                $event->confirmCode = $resetPasswordCodeModel;
                $this->trigger(self::EVENT_PASSWORD_RESET_CODE, $event);

            }

            return true;
        }

        return false;
    }


    /**
     * Get user with static cache
     *
     * @return User|bool|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByLogin($this->login);
        }

        return $this->_user;
    }

}
