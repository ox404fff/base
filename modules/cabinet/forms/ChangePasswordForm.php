<?php

namespace app\modules\cabinet\forms;
use app\base\behaviors\FormBehavior;
use app\base\web\ModelForm;
use app\modules\cabinet\components\events\ChangePasswordEvent;
use yii\base\Exception;
use yii\helpers\ArrayHelper;


/**
 * LoginForm is the model behind the login form.
 *
 * @mixin FormBehavior
 */
class ChangePasswordForm extends ModelForm
{

    /**
     * @var string current password
     */
    public $password;

    /**
     * @var string new password
     */
    public $newPassword;


    /**
     * On change password
     */
    const EVENT_CHANGE_PASSWORD = 'change_password';


    /**
     * @return array the behaviors.
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'form' => [
                'class'  => FormBehavior::className(),
                'events' => [
                    self::EVENT_CHANGE_PASSWORD => [
                        ['app\modules\cabinet\models\Settings', 'onChangePassword']
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
            [['newPassword', 'password'], 'required'],
            [['newPassword', 'password'], 'string', 'length' => [6, 64]],
            ['password', 'validatePassword'],
        ]);
    }


    /**
     * @return array the attribute labels
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'password'    => 'Current password',
            'newPassword' => 'New password',
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
     * Create change email settings
     *
     * @return bool
     * @throws Exception
     */
    public function doChange()
    {
        if ($this->validate()) {

            $this->user->password = \Yii::$app->getSecurity()->generatePasswordHash($this->newPassword);

            if (!$this->user->save()) {
                throw new Exception('Settings is temporarily unavailable');
            }

            $event = new ChangePasswordEvent();
            $event->user = $this->getUser();
            $this->trigger(self::EVENT_CHANGE_PASSWORD, $event);

            $this->password = null;
            $this->newPassword = null;

            return true;
        }

        return false;
    }


    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     */
    public function validatePassword($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !\Yii::$app->getSecurity()->validatePassword($this->password, $user->password)) {
                $this->addError($attribute, \Yii::t('app', 'Incorrect password'));
            }
        }
    }

}
