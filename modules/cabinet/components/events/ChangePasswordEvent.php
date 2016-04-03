<?php

namespace app\modules\cabinet\components\events;

use app\models\User;
use yii\base\Event;

/**
 * Event when user change password
 *
 * Class ChangeEmailEvent
 * @package app\modules\cabinet\components\events
 */
class ChangePasswordEvent extends Event
{

    /**
     * @var User - target user model
     */
    public $user;

}