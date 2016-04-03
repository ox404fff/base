<?php

namespace app\modules\cabinet\forms;

use app\base\behaviors\FormBehavior;
use app\base\web\ModelForm;
use app\modules\cabinet\models\Settings;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * BaseEmailForm base forms for email settings.
 */
class BaseEmailForm extends ModelForm
{

    /**
     * @var string - User email
     */
    public $email;

    /**
     * @return array the behaviors.
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'form' => [
                'class'  => FormBehavior::className(),
            ]
        ]);
    }


    /**
     * @var Settings - Change email setting request
     */
    protected $settingsChange;

    public function attributeLabels()
    {
        return [
            'email' => \Yii::t('app', 'E-mail'),
        ];
    }


    /**
     * @return array the validation rules.
     * @throws \Exception
     */
    public function rules()
    {
        $rules = [
            ['email', 'required'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'email'],
            ['email', 'string', 'length' => [6, 128]],
        ];

        if (empty($this->user)) {
            throw new \Exception(\Yii::t('app', 'User model is not set'));
        }

        $rules[] = ['email', 'app\modules\auth\components\validators\LoginExistValidator',
            'exclude' => $this->user->login
        ];

        return $rules;
    }



    /**
     * Return true if email confirmed
     *
     * return bool
     */
    public function isEmailConfirmed()
    {
        return $this->settingsChange->is_confirm;
    }



    /**
     * Set change settings history record
     *
     *
     * @param Settings $settingsChange
     *
     * @return bool
     * @throws \Exception
     */
    public function setSettingsChange(Settings $settingsChange)
    {
        if ($settingsChange->type != Settings::TYPE_CHANGE_EMAIL) {
            throw new \Exception(\Yii::t('app', 'Invalid settings change type'));
        }

        $this->settingsChange = $settingsChange;

        $this->email = $settingsChange->getJsonAttribute('email');

        return true;
    }



    /**
     * Get change settings history record
     *
     * @return Settings
     */
    public function getSettingsChange()
    {
        return $this->settingsChange;
    }

}
