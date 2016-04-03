<?php

namespace app\base\behaviors;

use app\base\db\ActiveRecord;
use app\base\helpers\DateTime;
use Yii;
use yii\base\Behavior;

/**
 * Behavior for check a limited lifetime records
 *
 * Class LifeTimeBehavior
 * @package app\base\behaviors
 */
class LifeTimeBehavior extends Behavior
{

    /**
     * @var string Created attribute
     */
    public $createdAtAttribute = 'created_at';

    /**
     * @var ActiveRecord
     */
    public $owner;


    /**
     * @var array Life time types to seconds
     */
    public $lifeTimesTypes = [];


    /**
     * if life time's end
     *
     * @param $type
     * @return bool
     * @throws \Exception
     */
    public function isExpired($type)
    {
        if (!isset($this->lifeTimesTypes[$type])) {
            return false;
        }

        $lifeTime = $this->lifeTimesTypes[$type];

        $fromTime = $this->owner->getAttribute($this->createdAtAttribute);
        return $fromTime + $lifeTime <= DateTime::time();
    }


    /**
     * Getting life time for type (in seconds)
     *
     * returned false if type not registered
     *
     * @param $type
     * @return bool|int
     */
    public function getLifeTime($type)
    {
        if (!isset($this->lifeTimesTypes[$type])) {
            return false;
        }

        return $this->lifeTimesTypes[$type];
    }


}
