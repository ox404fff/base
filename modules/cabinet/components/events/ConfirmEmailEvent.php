<?php

namespace app\modules\cabinet\components\events;

use app\models\User;
use app\modules\cabinet\models\Settings;
use yii\base\Event;

/**
 * Event end process change email address
 *
 * Class ChangeEmailEvent
 * @package app\modules\cabinet\components\events
 */
class ConfirmEmailEvent extends Event
{

    /**
     * @var User - target user model
     */
    public $user;

    /**
     * @var Settings settings change record
     */
    public $settingsChange;

    /**
     * @var string new email
     */
    public $email;

} 