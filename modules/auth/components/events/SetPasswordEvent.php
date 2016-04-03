<?php
namespace app\modules\auth\components\events;


use app\models\ConfirmCode;
use app\models\User;
use app\modules\cabinet\models\Settings;
use yii\base\Event;

/**
 * Event when user set new password with reset code
 *
 * Class SetPasswordEvent
 * @package app\modules\auth\components\events
 */
class SetPasswordEvent extends Event
{

    /**
     * @var User
     */
    public $user;

    /**
     * @var ConfirmCode
     */
    public $confirmCode;

    /**
     * @var Settings
     */
    public $settingChange;

} 