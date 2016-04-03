<?php

namespace test\base\behaviors;

use app\base\behaviors\LifeTimeBehavior;
use app\base\helpers\DateTime;
use test\base\db\ActiveRecordTestModel;
use yii\codeception\TestCase;

class LifeTimeBehaviorTest extends TestCase
{

    const ONE_DAY = 1;
    const TWO_DAY = 2;
    const THREE_DAY = 3;

    public function testIsExpired()
    {

        DateTime::$time = time();

        $activeRecord = new ActiveRecordTestModel();
        $activeRecord->created_at = DateTime::time() - DateTime::DAY * 2;

        $behavior = new LifeTimeBehavior();
        $behavior->lifeTimesTypes = [
            self::ONE_DAY   => DateTime::DAY,
            self::TWO_DAY   => DateTime::DAY * 2,
            self::THREE_DAY => DateTime::DAY * 3,
        ];

        $behavior->owner = $activeRecord;

        $this->assertTrue($behavior->isExpired(self::TWO_DAY),
            'Record created two days ago, should be expired for a lifetime two day');

        $this->assertTrue($behavior->isExpired(self::ONE_DAY),
            'Record created two days ago, should be expired for a lifetime one day');

        $this->assertFalse($behavior->isExpired(self::THREE_DAY),
            'Record created two days ago, should not be expired for a lifetime three days');

        $this->assertFalse($behavior->isExpired(null),
            'Record created two days ago, should not be expired for not set type');

    }


    public function testGetLifeTime()
    {
        $behavior = new LifeTimeBehavior();
        $behavior->lifeTimesTypes = [
            self::ONE_DAY   => DateTime::DAY,
            self::TWO_DAY   => DateTime::DAY * 2,
            self::THREE_DAY => DateTime::DAY * 3,
        ];

        $this->assertEquals($behavior->getLifeTime(self::ONE_DAY), DateTime::DAY);
        $this->assertEquals($behavior->getLifeTime(self::TWO_DAY), DateTime::DAY * 2);
        $this->assertEquals($behavior->getLifeTime(self::THREE_DAY), DateTime::DAY * 3);
    }


}
