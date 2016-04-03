<?php

namespace app\modules\cabinet\components\events;

use app\models\User;
use yii\base\Event;

/**
 * Event begin process change email address
 *
 * Class ChangeEmailEvent
 * @package app\modules\cabinet\components\events
 */
class ChangeEmailEvent extends Event
{

    /**
     * @var User - target user model
     */
    public $user;

    /**
     * @var string new email
     */
    public $email;

} 