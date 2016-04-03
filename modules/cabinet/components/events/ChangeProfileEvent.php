<?php

namespace app\modules\cabinet\components\events;

use app\models\User;
use yii\base\Event;

/**
 * Event on profile changed
 *
 * Class ChangeProfileEvent
 * @package app\modules\cabinet\components\events
 */
class ChangeProfileEvent extends Event
{

    /**
     * @var User - target user model
     */
    public $user;

} 