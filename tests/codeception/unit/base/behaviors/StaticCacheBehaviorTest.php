<?php

namespace test\base\behaviors;

use app\base\behaviors\StaticCacheBehavior;
use test\base\db\ActiveRecordTestModel;
use yii\codeception\TestCase;

class StaticCacheBehaviorTest extends TestCase
{

    protected function setUp()
    {
        parent::setUp();
    }


    public $key11 = 'testRecordKey11';
    public $key12 = 'testRecordKey12';
    public $key13 = 'testRecordKey13';
    public $key21 = 'testRecordKey21';
    public $key22 = 'testRecordKey22';
    public $key23 = 'testRecordKey23';
    public $key31 = 'testRecordKey31';
    public $key32 = 'testRecordKey32';
    public $key33 = 'testRecordKey33';
    

    public function testStaticCache()
    {
        $staticCacheBehavior = $this->_createBehaviorClass();

        $testRecords = $this->getTestRecordsArray();

        /**
         * Set to cache
         */
        $result = $staticCacheBehavior->staticCache(function() use ($testRecords) {
            return $testRecords[$this->key11][$this->key21][$this->key31];
        }, [$this->key11, $this->key21, $this->key31]);

        $this->assertEquals($result, 'testRecordValue1',
            'Result call static cache function should equals testRecordValue1');

        $result = $staticCacheBehavior->staticCache(function() use ($testRecords) {
            return $testRecords[$this->key11][$this->key22][$this->key31];
        }, [$this->key11, $this->key22, $this->key31]);

        $this->assertEquals($result, 'testRecordValue2',
            'Result call static cache function should equals testRecordValue2');


        $testRecords = null;

        /**
         * Get from cache
         */
        $result = $staticCacheBehavior->staticCache(function() use ($testRecords) {

            $this->assertFalse(true,
                'This assert should not be call');

            return $testRecords[$this->key11][$this->key21][$this->key31];
        }, [$this->key11, $this->key21, $this->key31]);

        $this->assertEquals($result, 'testRecordValue1',
            'Result call static cache function should equals testRecordValue1');

        $result = $staticCacheBehavior->staticCache(function() use ($testRecords) {

            $this->assertFalse(true,
                'This assert should not be call');

            return $testRecords[$this->key11][$this->key22][$this->key31];
        }, [$this->key11, $this->key22, $this->key31]);

        $this->assertEquals($result, 'testRecordValue2',
            'Result call static cache function should equals testRecordValue2');

    }



    public function testClearCache()
    {
        $staticCacheBehavior = $this->_createBehaviorClass();

        $testRecords = $this->getTestRecordsArray();

        /**
         * Set to cache
         */
        $result = $staticCacheBehavior->staticCache(function() use ($testRecords) {
            return $testRecords[$this->key11][$this->key21][$this->key31];
        }, [$this->key11, $this->key21, $this->key31]);

        $this->assertEquals($result, 'testRecordValue1',
            'Result call static cache function should equals testRecordValue1');

        $result = $staticCacheBehavior->staticCache(function() use ($testRecords) {
            return $testRecords[$this->key11][$this->key22][$this->key31];
        }, [$this->key11, $this->key22, $this->key31]);

        $this->assertEquals($result, 'testRecordValue2',
            'Result call static cache function should equals testRecordValue2');


        $testRecords = null;
        $staticCacheBehavior->clearCache();

        /**
         * Get from cache
         */
        $result = $staticCacheBehavior->staticCache(function() use ($testRecords) {

            $this->assertTrue(true,
                'This assert should be call');

            return $testRecords[$this->key11][$this->key21][$this->key31];
        }, [$this->key11, $this->key21, $this->key31]);

        $this->assertNull($result,
            'Result call static cache function should equals null');

        $result = $staticCacheBehavior->staticCache(function() use ($testRecords) {

            $this->assertTrue(true,
                'This assert should be call');

            return $testRecords[$this->key11][$this->key22][$this->key31];
        }, [$this->key11, $this->key22, $this->key31]);

        $this->assertNull($result,
            'Result call static cache function should equals null');

    }


    
    private function getTestRecordsArray()
    {
        return  [
            $this->key11 => [
                $this->key21 => [
                    $this->key31 => 'testRecordValue1',
                    $this->key32 => 'testValue',
                    $this->key33 => 'testValue',
                ],
                $this->key22 => [
                    $this->key31 => 'testRecordValue2',
                    $this->key32 => 'testValue',
                    $this->key33 => 'testValue',
                ],
                $this->key23 => [
                    $this->key31 => 'testRecordValue3',
                    $this->key32 => 'testValue',
                    $this->key33 => 'testValue',
                ],
            ],
            $this->key12 => [
                $this->key21 => [
                    $this->key31 => 'testValue',
                    $this->key32 => 'testValue',
                    $this->key33 => 'testValue',
                ],
                $this->key22 => [
                    $this->key31 => 'testValue',
                    $this->key32 => 'testValue',
                    $this->key33 => 'testValue',
                ],
                $this->key23 => [
                    $this->key31 => 'testValue',
                    $this->key32 => 'testValue',
                    $this->key33 => 'testValue',
                ],
            ],
            $this->key13 => [
                $this->key21 => [
                    $this->key31 => 'testValue',
                    $this->key32 => 'testValue',
                    $this->key33 => 'testValue',
                ],
                $this->key22 => [
                    $this->key31 => 'testValue',
                    $this->key32 => 'testValue',
                    $this->key33 => 'testValue',
                ],
                $this->key23 => [
                    $this->key31 => 'testValue',
                    $this->key32 => 'testValue',
                    $this->key33 => 'testValue',
                ],
            ],
        ]; 
    }


    /**
     * @return \app\base\behaviors\StaticCacheBehavior
     */
    private function _createBehaviorClass()
    {
        $staticCacheBehavior = new StaticCacheBehavior();
        /**
         * @var \app\base\behaviors\StaticCacheBehavior $staticCacheBehavior
         */

        $staticCacheBehavior->owner = new ActiveRecordTestModel();

        return $staticCacheBehavior;
    }

}
