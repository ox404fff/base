<?php

namespace app\modules\auth\forms;

use app\base\behaviors\FormBehavior;
use app\base\web\ModelForm;
use app\models\User;
use app\modules\auth\components\events\LoginEvent;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * LoginForm is the model behind the login form.
 *
 * @mixin FormBehavior
 */
class LoginForm extends ModelForm
{

    /**
     * @var string User login
     */
    public $login;

    /**
     * @var string User secret password
     */
    public $password;

    /**
     * @var bool Login for a month
     */
    public $rememberMe = true;

    /**
     * @var bool Static cache with user model
     */
    private $_user = false;

    /**
     * Event when user successfully login
     */
    const EVENT_LOGIN = 'login';


    /**
     * @return array the behaviors.
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'form' => [
                'class'  => FormBehavior::className(),
                'events' => [
                    self::EVENT_LOGIN => [
                    ],
                ]
            ]
        ]);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'login'      => \Yii::t('app', 'E-mail'),
            'password'   => \Yii::t('app', 'Password'),
            'rememberMe' => \Yii::t('app', 'Remember me')
        ];
    }


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['login', 'password'], 'required'],
            ['login', 'app\modules\auth\components\validators\LoginValidator'],
            ['login', 'string', 'length' => [6, 128]],

            ['password', 'string', 'length' => [6, 64]],
            ['password', 'validatePassword'],
            ['rememberMe', 'boolean'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !\Yii::$app->getSecurity()->validatePassword($this->password, $user->password)) {
                $this->addError($attribute, \Yii::t('app', 'Incorrect login or password.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function doLogin()
    {
        if ($this->validate()) {
            $result = Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);

            if ($result) {
                $event = new LoginEvent();
                $event->user = $this->getUser();
                $this->trigger(self::EVENT_LOGIN, $event);
            }

            return $result;
        }
        return false;
    }

    /**
     * Finds user by [[login]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByLogin($this->login);
        }

        return $this->_user;
    }
}
