<?php

namespace app\base\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use yii\helpers\Json;

/**
 * Behavior for static cache
 *
 * Class StaticCacheBehavior
 * @package app\base\behaviors
 */
class StaticCacheBehavior extends Behavior
{

    /**
     * Cache storage
     *
     * @var array
     */
    private static $_cache = array();


    public function init()
    {
        return parent::init();
    }


    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'clearCache',
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'clearCache',
        ];
    }


    /**
     * Get data with static cache
     *
     * @param $method
     * @param $arguments
     * @return bool|mixed
     */
    public function staticCache($method, $arguments)
    {
        $result = $this->_getFromCache($arguments);

        if ($result !== false) {
            return $result;
        }

        $result = call_user_func($method);

        $this->_setToCache($arguments, $result);

        return $result;
    }


    /**
     * Clear cache for this instance
     */
    public function clearCache()
    {
        self::$_cache[$this->owner->className()] = null;

        \Yii::info('Clear static cache: ['.$this->owner->className().']', $this->className());
    }


    /**
     * Get data from cache
     *
     * @param $arguments
     * @return bool
     */
    private function _getFromCache($arguments)
    {
        if (!isset(self::$_cache[$this->owner->className()])) {
            return false;
        }

        $items = &self::$_cache[$this->owner->className()];

        $usedKeys = array();
        foreach ($arguments as $arg) {
            $arg = $this->_processArg($arg);
            if (!isset($items[$arg])) {
                return false;
            }
            $usedKeys[] = $arg;
            $items = &$items[$arg];
        }

        \Yii::info('Get data from static cache: ['.$this->owner->className().']['.implode('][', $usedKeys).']', $this->className());

        return $items;
    }


    /**
     * Set data to cache
     *
     * @param $arguments
     * @param $data
     */
    private function _setToCache($arguments, $data)
    {
        if (!isset(self::$_cache[$this->owner->className()])) {
            self::$_cache[$this->owner->className()] = array();
        }

        $items = &self::$_cache[$this->owner->className()];

        $usedKeys = array();
        foreach ($arguments as $arg) {
            $arg = $this->_processArg($arg);
            if (!isset($items[$arg])) {
                $items[$arg] = array();
            }
            $usedKeys[] = $arg;
            $items = &$items[$arg];
        }

        \Yii::info('Set data to static cache: ['.$this->owner->className().']['.implode('][', $usedKeys).']', $this->className());

        $items = $data;
    }


    /**
     * Convert arguments to string
     *
     * @param $arg
     *
     * @return string
     */
    private function _processArg($arg)
    {
        if (is_array($arg)) {
            $arg = Json::encode($arg);
        }
        return $arg;
    }



}
