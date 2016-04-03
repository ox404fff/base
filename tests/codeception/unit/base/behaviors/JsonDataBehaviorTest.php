<?php

namespace test\base\behaviors;

use app\base\behaviors\JsonDataBehavior;
use test\base\db\ActiveRecordTestModel;
use yii\codeception\TestCase;
use yii\helpers\Json;

class JsonDataBehaviorTest extends TestCase
{

    private $_testAttributes = [
        'test1' => 'data1',
        'test2' => 'data2',
        'test3' => 'data3',
    ];

    public function testGetJsonAttribute()
    {
        $model = $this->_createModel();
        $this->assertNull($model->getJsonAttribute('not_exists_attribute'));

        $this->assertEquals($model->getJsonAttribute('test1'), 'data1');
        $this->assertEquals($model->getJsonAttribute('test2'), 'data2');
        $this->assertEquals($model->getJsonAttribute('test3'), 'data3');
    }

    public function testGetJsonAttributeEmpty()
    {
        $model = $this->_createModel(false);
        $this->assertNull($model->getJsonAttribute('not_exists_attribute'));
    }


    public function testSetJsonAttribute()
    {
        $model = $this->_createModel();
        $model->setJsonAttribute('new_test', 'new_data');
        $model->setJsonAttribute('test1', 'new_data1');

        $this->assertEquals($model->getJsonAttribute('test2'), 'data2');
        $this->assertEquals($model->getJsonAttribute('new_test'), 'new_data');
        $this->assertEquals($model->getJsonAttribute('test1'), 'new_data1');
    }

    public function testSetJsonAttributeEmpty()
    {
        $model = $this->_createModel(false);
        $model->setJsonAttribute('new_test', 'new_data');
        $this->assertEquals($model->getJsonAttribute('new_test'), 'new_data');
    }


    public function testGetJsonAttributes()
    {
        $model = $this->_createModel();
        $this->assertEquals($model->getJsonAttributes(), $this->_testAttributes);
    }


    public function testGetJsonAttributesEmpty()
    {
        $model = $this->_createModel(false);
        $this->assertTrue(is_array($model->getJsonAttributes()));
        $this->assertEmpty($model->getJsonAttributes());
    }


    public function testSetJsonAttributes()
    {
        $model = $this->_createModel();
        $model->setJsonAttributes([
            'new_test1' => 'new_data1',
            'new_test2' => 'new_data2',
        ]);

        $this->assertNotEquals($model->getJsonAttribute('test2'), 'data2');
        $this->assertEquals($model->getJsonAttribute('new_test1'), 'new_data1');
    }


    public function testSetJsonAttributesEmpty()
    {
        $model = $this->_createModel(false);
        $model->setJsonAttributes($this->_testAttributes);

        $this->assertEquals($model->getJsonAttributes(), $this->_testAttributes);
    }


    /**
     * @return ActiveRecord|JsonDataBehavior
     */
    private function _createModel($setAttributes = true)
    {
        $model = new ActiveRecordTestModel();
        if ($setAttributes) {
            $model->text = Json::encode($this->_testAttributes);
        }

        $model->attachBehavior('json_data', [
            'class'     => JsonDataBehavior::className(),
            'attribute' => 'text'
        ]);

        return $model;
    }

}
