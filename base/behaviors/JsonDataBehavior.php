<?php

namespace app\base\behaviors;

use Yii;
use yii\base\Behavior;
use yii\helpers\Json;
use yii\helpers\StringHelper;

/**
 * Behavior for store json data
 *
 * Class JsonDataBehavior
 * @package app\base\behaviors
 */
class JsonDataBehavior extends Behavior
{

    /**
     * @var string model attribute name
     */
    public $attribute = 'json_data';

    /**
     * @var array|bool Cache for json attributes
     */
    protected $_jsonAttributes = false;


    /**
     * Get json attribute
     *
     * @param $name
     *
     * @return mixed
     */
    public function getJsonAttribute($name)
    {
        $this->_unpackJsonData();
        return isset($this->_jsonAttributes[$name]) ? $this->_jsonAttributes[$name] : null;
    }


    /**
     * Set json attribute
     *
     * @param $name
     * @param $value
     */
    public function setJsonAttribute($name, $value)
    {
        $this->_unpackJsonData();
        $this->_jsonAttributes[$name] = $value;
        $this->_packJsonData();
    }


    /**
     * Get all json attributes
     *
     * @return array
     */
    public function getJsonAttributes()
    {
        $this->_unpackJsonData();
        return $this->_jsonAttributes;
    }


    /**
     * Set all json data
     *
     * @param $attributes
     *
     * @return array
     */
    public function setJsonAttributes($attributes)
    {
        $this->_jsonAttributes = $attributes;
        $this->_packJsonData();
    }


    /**
     * Unpack and cache attributes
     */
    private function _unpackJsonData()
    {
        if ($this->_jsonAttributes === false) {
            $json = $this->owner->getAttribute($this->attribute);
            $this->_jsonAttributes = empty($json) ? [] : Json::decode($json, true);
        }
    }

    /**
     * Unpack and cache attributes
     */
    private function _packJsonData()
    {
        $this->owner->setAttribute($this->attribute, Json::encode(empty($this->_jsonAttributes) ? [] : $this->_jsonAttributes));
    }

}
