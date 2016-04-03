<?php

namespace test\base\behaviors;

use app\base\behaviors\FormBehavior;
use test\base\db\ActiveRecordTestModel;
use test\fixtures\UserFixture;
use yii\codeception\TestCase;

class FormBehaviorTest extends TestCase
{

    const EVENT_TEST = 'test';

    protected static $isEventExecuted = false;


    public function fixtures()
    {
        return [
            'user' => UserFixture::className(),
        ];
    }


    public function testSetUser()
    {
        $model = new ActiveRecordTestModel();
        $model->attachBehavior('form', [
            'class' => FormBehavior::className(),
        ]);
        $inactiveUser = $this->getFixture('user')->getModel(UserFixture::ID_USER_INACTIVE);

        /**
         * "clone" for check is changed attributes after set
         */
        $this->assertTrue($model->setUser(clone $inactiveUser));

        $this->assertEquals($model->getUser()->getAttributes(), $inactiveUser->getAttributes());
    }


    public function testGetUser()
    {
        $model = new ActiveRecordTestModel();
        $model->attachBehavior('form', [
            'class' => FormBehavior::className(),
        ]);

        $this->assertNull($model->getUser());

        $inactiveUser = $this->getFixture('user')->getModel(UserFixture::ID_USER_INACTIVE);
        $this->assertTrue($model->setUser($inactiveUser));
        $this->assertNotNull($model->getUser());
    }

    public function testAttachEvents()
    {
        $model = new ActiveRecordTestModel();

        $model->attachBehavior('form', [
            'class' => FormBehavior::className(),
            'events' => [
                self::EVENT_TEST => [
                    [self::class, 'onTestEvent'],
                ]
            ]
        ]);

        $this->assertFalse(self::$isEventExecuted);

        $model->trigger(self::EVENT_TEST);

        $this->assertTrue(self::$isEventExecuted);

    }


    public static function onTestEvent()
    {
        self::$isEventExecuted = true;
    }
}
