<?php

namespace app\modules\auth\forms;

use app\base\behaviors\FormBehavior;
use app\base\web\ModelForm;
use app\models\User;
use app\modules\auth\components\events\RegistrationEvent;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * RegistrationForm is the model behind the registration form.
 *
 * @mixin FormBehavior
 */
class RegistrationForm extends ModelForm
{

    /**
     * @var string new user login
     */
    public $login;


    /**
     * @var string new user password
     */
    public $password;


    /**
     * Event, when new user successfully registered
     */
    const EVENT_REGISTRATION_SUCCESS = 'registration_success';


    /**
     * @return array the behaviors.
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'form' => [
                'class'  => FormBehavior::className(),
                'events' => [
                    self::EVENT_REGISTRATION_SUCCESS => [
                        ['\app\services\ConfirmCodeService', 'onRegistration'],
                        ['\app\modules\cabinet\models\Settings', 'onRegistration'],
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
            [['login', 'password'], 'required'],

            [['login'], 'filter', 'filter' => 'trim'],

            ['password', 'string', 'length' => [6, 64]],

            ['login', 'string', 'length' => [6, 128]],
            ['login', 'app\modules\auth\components\validators\LoginValidator'],
            ['login', 'app\modules\auth\components\validators\LoginExistValidator', 'skipOnError' => true]

        ];
    }


    public function attributeLabels()
    {
        return [
            'login'    => \Yii::t('app', 'E-mail'),
            'password' => \Yii::t('app', 'Password'),
        ];
    }


    /**
     * Register a user
     *
     * @return bool user registration is successfully
     * @throws Exception when can not save new user record
     */
    public function doRegistration()
    {
        if ($this->validate()) {

            $user = $this->_createUserModel();

            $this->setUser($user);

            $event = new RegistrationEvent();

            $event->user = $user;

            $this->trigger(self::EVENT_REGISTRATION_SUCCESS, $event);

            return true;

        }

        return false;
    }


    /**
     * Created new inactive user model
     *
     * @return User
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    protected function _createUserModel()
    {
        $user = User::createUser($this->login, \Yii::$app->getSecurity()->generatePasswordHash($this->password));

        if (!$user->save()) {
            throw new Exception(\Yii::t('app', 'Registration is temporarily unavailable'));
        }
        return $user;
    }

}
