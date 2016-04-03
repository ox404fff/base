<?php

namespace app\base\behaviors;

use app\models\User;
use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use yii\helpers\Json;
use yii\helpers\StringHelper;

/**
 * Behavior for form models
 *
 * Class FormBehavior
 * @package app\base\behaviors
 */
class FormBehavior extends Behavior
{


    /**
     * @var User - User model
     */
    protected $user;

    /**
     * @var bool
     */
    private $_isEventsAttached = false;


    /**
     * Events attached when initialisation
     *
     * @var array
     */
    public $events = [];



    /**
     * Set user model
     *
     * @param User $user
     *
     * @return bool
     */
    public function setUser(User $user)
    {
        if (empty($user)) {
            return false;
        }

        $this->user = $user;
        return true;
    }


    /**
     * Get user model
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }



    /**
     * @inheritdoc
     */
    public function attach($owner)
    {
        parent::attach($owner);

        $this->_attachEvents();
    }


    /**
     * Attach events
     */
    private function _attachEvents()
    {
        if ($this->_isEventsAttached) {
            return;
        }

        $this->_isEventsAttached = true;

        foreach ($this->events as $name => $handlers) {
            foreach ($handlers as $handler) {
                $this->owner->on($name, $handler);
            }
        }
    }

}
