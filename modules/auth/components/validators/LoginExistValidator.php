<?php

namespace app\modules\auth\components\validators;

use app\models\User;
use yii\validators\Validator;


/**
 * Validate is login exists
 */
class LoginExistValidator extends Validator
{

    /**
     * Exclude list
     *
     * @var null
     */
    public $exclude = array();

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->exclude = (array) $this->exclude;
    }

    /**
     * Check is with this login already registered
     *
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        $valid = true;

        $user = User::findByLogin($value, User::$activeUserTypes);
        if (!empty($user) && !in_array($value, $this->exclude)) {
            $valid = false;
            $this->message = \Yii::t('app', 'User with this e-mail address already registered.');
        }

        return $valid ? null : [$this->message, []];
    }


}
