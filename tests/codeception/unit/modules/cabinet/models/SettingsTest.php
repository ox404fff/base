<?php

namespace test\modules\cabinet\models;

use app\base\behaviors\LifeTimeBehavior;
use app\base\db\ActiveRecord;
use app\base\helpers\DateTime;
use app\modules\cabinet\models\Settings;
use test\fixtures\SettingsFixture;
use yii\base\Event;
use yii\codeception\TestCase;
use yii\helpers\ArrayHelper;

class SettingsTest extends TestCase
{

    public function fixtures()
    {
        return [
            'settings' => SettingsFixture::className(),
        ];
    }

    public function setUp()
    {
        parent::setUp();

        Event::on(Settings::className(), Settings::EVENT_INIT, function(Event $event) {
            $event->sender->attachBehavior('life_time', [
                'class'              => LifeTimeBehavior::className(),
                'createdAtAttribute' => Settings::ATTRIBUTE_CREATED_AT,
                'lifeTimesTypes'     => [
                    SettingsFixture::TYPE_CHANGE => DateTime::DAY,
                    SettingsFixture::TYPE_CHANGE_2 => DateTime::DAY,
                    SettingsFixture::TYPE_CHANGE_3 => DateTime::DAY,
                    SettingsFixture::TYPE_CHANGE_4 => DateTime::DAY,
                    SettingsFixture::TYPE_CHANGE_5 => DateTime::DAY,
                    SettingsFixture::TYPE_CHANGE_6 => DateTime::DAY,
                ]
            ]);
        });
    }


    public function testDataStructure()
    {
        $lastConfirmed = Settings::getLastAll(SettingsFixture::OTHER_USER_ID, true);
        $lastUnConfirmed = Settings::getLastAll(SettingsFixture::OTHER_USER_ID, false);
        $lastAll = Settings::getLastAll(SettingsFixture::OTHER_USER_ID);

        $this->assertEmpty(
            array_diff(
                $lastAll,
                ArrayHelper::merge($lastConfirmed, $lastUnConfirmed)
            )
        );
    }


    public function testCreate()
    {

        $model = Settings::create(SettingsFixture::USER_ID, Settings::TYPE_CHANGE_EMAIL, ['email' => 'test@example.com'], false);

        $this->assertNotEmpty($model);
        $this->assertTrue($model->save());
        $this->assertEquals($model->getJsonAttribute('email'), 'test@example.com');

    }


    public function testGetLastConfirmed()
    {

        $model = Settings::getLast(SettingsFixture::USER_ID, Settings::TYPE_CHANGE_EMAIL, true);

        $this->assertNotEmpty($model);
        $this->assertEquals($model->getJsonAttribute('email'), 'confirmed_email2@example.com');

    }


    public function testGetLastUnconfirmed()
    {
        $model = Settings::getLast(SettingsFixture::USER_ID, Settings::TYPE_CHANGE_EMAIL, false);

        $this->assertNotEmpty($model);
        $this->assertEquals($model->getJsonAttribute('email'), 'unconfirmed_email2@example.com');
    }


    public function testGetLastAny()
    {
        $model = Settings::getLast(SettingsFixture::USER_ID, Settings::TYPE_CHANGE_EMAIL);

        $this->assertNotEmpty($model);
        $this->assertEquals($model->getJsonAttribute('email'), 'confirmed_email2@example.com');
    }


    public function testIsConfirmedLast()
    {
        $lastChange = Settings::create(SettingsFixture::USER_ID, SettingsFixture::TYPE_CHANGE, ['test' => 'test'], false);
        $this->assertTrue($lastChange->save());

        $this->assertFalse(Settings::isConfirmedLast(SettingsFixture::USER_ID, SettingsFixture::TYPE_CHANGE));

        $lastChange = Settings::create(SettingsFixture::USER_ID, SettingsFixture::TYPE_CHANGE, true);
        $this->assertTrue($lastChange->save());

        $this->assertTrue(Settings::isConfirmedLast(SettingsFixture::USER_ID, SettingsFixture::TYPE_CHANGE));
    }


    public function testIsConfirmedLastTimeIsOver()
    {
        $lastChange = new Settings();

        $lastChange->attachBehavior('life_time' , [
            'class'              => LifeTimeBehavior::className(),
            'createdAtAttribute' => ActiveRecord::ATTRIBUTE_CREATED_AT,
            'lifeTimesTypes'     => [
                SettingsFixture::TYPE_CHANGE => DateTime::DAY
            ]
        ]);

        $result = $lastChange::isConfirmedLast(SettingsFixture::OTHER_USER_ID_2, SettingsFixture::TYPE_CHANGE);

        $this->assertNull($result);
    }


    public function testRollbackSettingsChangesNormal()
    {
        $lastChange = Settings::create(SettingsFixture::USER_ID, SettingsFixture::TYPE_CHANGE, ['test' => 'test1'], false);
        $this->assertTrue($lastChange->save());

        $lastChange = Settings::create(SettingsFixture::USER_ID, SettingsFixture::TYPE_CHANGE, ['test' => 'test2'], false);
        $this->assertTrue($lastChange->save());

        $this->assertTrue(Settings::rollbackSettingsChanges(SettingsFixture::USER_ID, SettingsFixture::TYPE_CHANGE));
        $this->assertTrue(Settings::isConfirmedLast(SettingsFixture::USER_ID, SettingsFixture::TYPE_CHANGE));
    }

    public function testRollbackSettingsChangesLastConfirmed()
    {
        $lastChange = Settings::create(SettingsFixture::USER_ID, SettingsFixture::TYPE_CHANGE, ['test' => 'test'], true);
        $this->assertTrue($lastChange->save());

        $this->assertNull(Settings::rollbackSettingsChanges(SettingsFixture::USER_ID, SettingsFixture::TYPE_CHANGE));

        $lastChange = Settings::create(SettingsFixture::NOT_EXISTS_USER_ID, SettingsFixture::TYPE_CHANGE, ['test' => 'test'], true);
        $this->assertTrue($lastChange->save());

        $this->assertNull(Settings::rollbackSettingsChanges(SettingsFixture::USER_ID, SettingsFixture::TYPE_CHANGE));
    }


    public function testRollbackSettingsChangesFail()
    {
        $this->assertFalse(Settings::rollbackSettingsChanges(SettingsFixture::NOT_EXISTS_USER_ID, SettingsFixture::TYPE_CHANGE));
    }


    public function testIsExistConfirmedSettings()
    {
        $this->assertFalse(Settings::isExistConfirmedSettings(SettingsFixture::NOT_EXISTS_USER_ID, SettingsFixture::TYPE_CHANGE));
        $this->assertTrue(Settings::isExistConfirmedSettings(SettingsFixture::USER_ID, SettingsFixture::TYPE_CHANGE));

        $lastChange = Settings::create(SettingsFixture::NOT_EXISTS_USER_ID, SettingsFixture::TYPE_CHANGE, ['test' => 'test'], false);
        $this->assertTrue($lastChange->save());

        $this->assertFalse(Settings::isExistConfirmedSettings(SettingsFixture::NOT_EXISTS_USER_ID, SettingsFixture::TYPE_CHANGE));

        $lastChange->is_confirm = true;
        $this->assertTrue($lastChange->save());
        $this->assertTrue(Settings::isExistConfirmedSettings(SettingsFixture::NOT_EXISTS_USER_ID, SettingsFixture::TYPE_CHANGE));
    }


    public function testGetByIds()
    {
        $condition = Settings::find();
        $condition->where(['user_id' => SettingsFixture::USER_ID]);
        $condition->indexBy('id');
        $result1 = $condition->all();

        $result2 = Settings::getByIds(SettingsFixture::USER_ID, array_keys($result1));

        foreach ($result2 as $id => $row) {
            $this->assertEquals($id, $row->id);
            $this->assertArrayHasKey($id, $result1);
            unset($result1[$id]);
        }

        $this->assertEmpty($result1);

    }


    public function testGetLastAll()
    {
        $lastSettingsIds = Settings::getLastAll(SettingsFixture::OTHER_USER_ID);

        $settings = Settings::getByIds(SettingsFixture::OTHER_USER_ID, $lastSettingsIds);

        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE, $lastSettingsIds);
        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_2, $lastSettingsIds);
        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_3, $lastSettingsIds);
        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_4, $lastSettingsIds);
        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_5, $lastSettingsIds);
        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_6, $lastSettingsIds);

        $test = $settings[$lastSettingsIds[SettingsFixture::TYPE_CHANGE]]->getJsonAttribute('test');
        $this->assertEquals($test, 'test1');

        $test = $settings[$lastSettingsIds[SettingsFixture::TYPE_CHANGE_2]]->getJsonAttribute('test');
        $this->assertEquals($test, 'test3');

        $test = $settings[$lastSettingsIds[SettingsFixture::TYPE_CHANGE_3]]->getJsonAttribute('test');
        $this->assertEquals($test, 'test4');

        $test = $settings[$lastSettingsIds[SettingsFixture::TYPE_CHANGE_4]]->getJsonAttribute('test');
        $this->assertEquals($test, 'test5');

        $test = $settings[$lastSettingsIds[SettingsFixture::TYPE_CHANGE_5]]->getJsonAttribute('test');
        $this->assertEquals($test, 'test6');

        $test = $settings[$lastSettingsIds[SettingsFixture::TYPE_CHANGE_6]]->getJsonAttribute('test');
        $this->assertEquals($test, 'test10');

    }


    public function testGetLastAllConfirmed()
    {
        $lastSettingsIds = Settings::getLastAll(SettingsFixture::OTHER_USER_ID, true);

        $settings = Settings::getByIds(SettingsFixture::OTHER_USER_ID, $lastSettingsIds);

        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE, $lastSettingsIds);
        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_2, $lastSettingsIds);
        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_3, $lastSettingsIds);
        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_5, $lastSettingsIds);
        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_6, $lastSettingsIds);

        $this->assertArrayNotHasKey(SettingsFixture::TYPE_CHANGE_4, $lastSettingsIds);


        $test = $settings[$lastSettingsIds[SettingsFixture::TYPE_CHANGE]]->getJsonAttribute('test');
        $this->assertEquals($test, 'test0');

        $test = $settings[$lastSettingsIds[SettingsFixture::TYPE_CHANGE_2]]->getJsonAttribute('test');
        $this->assertEquals($test, 'test2');

        $test = $settings[$lastSettingsIds[SettingsFixture::TYPE_CHANGE_3]]->getJsonAttribute('test');
        $this->assertEquals($test, 'test4');

        $test = $settings[$lastSettingsIds[SettingsFixture::TYPE_CHANGE_5]]->getJsonAttribute('test');
        $this->assertEquals($test, 'test6');

        $test = $settings[$lastSettingsIds[SettingsFixture::TYPE_CHANGE_6]]->getJsonAttribute('test');
        $this->assertEquals($test, 'test7');

    }


    public function testGetLastAllUnConfirmed()
    {
        $lastSettingsIds = Settings::getLastAll(SettingsFixture::OTHER_USER_ID, false);

        $settings = Settings::getByIds(SettingsFixture::OTHER_USER_ID, $lastSettingsIds);

        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE, $lastSettingsIds);
        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_2, $lastSettingsIds);
        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_4, $lastSettingsIds);
        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_5, $lastSettingsIds);
        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_6, $lastSettingsIds);

        $this->assertArrayNotHasKey(SettingsFixture::TYPE_CHANGE_3, $lastSettingsIds);


        $test = $settings[$lastSettingsIds[SettingsFixture::TYPE_CHANGE]]->getJsonAttribute('test');
        $this->assertEquals($test, 'test1');

        $test = $settings[$lastSettingsIds[SettingsFixture::TYPE_CHANGE_2]]->getJsonAttribute('test');
        $this->assertEquals($test, 'test3');

        $test = $settings[$lastSettingsIds[SettingsFixture::TYPE_CHANGE_4]]->getJsonAttribute('test');
        $this->assertEquals($test, 'test5');

        $test = $settings[$lastSettingsIds[SettingsFixture::TYPE_CHANGE_6]]->getJsonAttribute('test');
        $this->assertEquals($test, 'test10');
    }


    public function testGetAll()
    {

        $setting = Settings::getAll(SettingsFixture::OTHER_USER_ID);

        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE, $setting);
        $this->assertFalse($setting[SettingsFixture::TYPE_CHANGE]->is_confirm);
        $this->assertEquals($setting[SettingsFixture::TYPE_CHANGE]->getJsonAttribute('test'), 'test1');

        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_2, $setting);
        $this->assertTrue($setting[SettingsFixture::TYPE_CHANGE_2]->is_confirm);
        $this->assertEquals($setting[SettingsFixture::TYPE_CHANGE_2]->getJsonAttribute('test'), 'test2');

        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_3, $setting);
        $this->assertTrue($setting[SettingsFixture::TYPE_CHANGE_3]->is_confirm);
        $this->assertEquals($setting[SettingsFixture::TYPE_CHANGE_3]->getJsonAttribute('test'), 'test4');

        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_4, $setting);
        $this->assertFalse($setting[SettingsFixture::TYPE_CHANGE_4]->is_confirm);
        $this->assertEquals($setting[SettingsFixture::TYPE_CHANGE_4]->getJsonAttribute('test'), 'test5');

        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_5, $setting);
        $this->assertTrue($setting[SettingsFixture::TYPE_CHANGE_5]->is_confirm);
        $this->assertEquals($setting[SettingsFixture::TYPE_CHANGE_5]->getJsonAttribute('test'), 'test6');

        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_6, $setting);
        $this->assertFalse($setting[SettingsFixture::TYPE_CHANGE_6]->is_confirm);
        $this->assertEquals($setting[SettingsFixture::TYPE_CHANGE_6]->getJsonAttribute('test'), 'test10');
    }


    public function testGetAllConfirmed()
    {
        $setting = Settings::getAll(SettingsFixture::OTHER_USER_ID, true);

        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE, $setting);
        $this->assertTrue($setting[SettingsFixture::TYPE_CHANGE]->is_confirm);
        $this->assertEquals($setting[SettingsFixture::TYPE_CHANGE]->getJsonAttribute('test'), 'test0');

        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_2, $setting);
        $this->assertTrue($setting[SettingsFixture::TYPE_CHANGE_2]->is_confirm);
        $this->assertEquals($setting[SettingsFixture::TYPE_CHANGE_2]->getJsonAttribute('test'), 'test2');

        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_3, $setting);
        $this->assertTrue($setting[SettingsFixture::TYPE_CHANGE_3]->is_confirm);
        $this->assertEquals($setting[SettingsFixture::TYPE_CHANGE_3]->getJsonAttribute('test'), 'test4');

        $this->assertArrayNotHasKey(SettingsFixture::TYPE_CHANGE_4, $setting);

        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_5, $setting);
        $this->assertTrue($setting[SettingsFixture::TYPE_CHANGE_5]->is_confirm);
        $this->assertEquals($setting[SettingsFixture::TYPE_CHANGE_5]->getJsonAttribute('test'), 'test6');

        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_6, $setting);
        $this->assertTrue($setting[SettingsFixture::TYPE_CHANGE_6]->is_confirm);
        $this->assertEquals($setting[SettingsFixture::TYPE_CHANGE_6]->getJsonAttribute('test'), 'test7');

    }


    public function testGetAllUnConfirmed()
    {
        $setting = Settings::getAll(SettingsFixture::OTHER_USER_ID, false);

        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE, $setting);
        $this->assertFalse($setting[SettingsFixture::TYPE_CHANGE]->is_confirm);
        $this->assertEquals($setting[SettingsFixture::TYPE_CHANGE]->getJsonAttribute('test'), 'test1');

        $this->assertArrayNotHasKey(SettingsFixture::TYPE_CHANGE_2, $setting);

        $this->assertArrayNotHasKey(SettingsFixture::TYPE_CHANGE_3, $setting);

        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_4, $setting);
        $this->assertFalse($setting[SettingsFixture::TYPE_CHANGE_4]->is_confirm);
        $this->assertEquals($setting[SettingsFixture::TYPE_CHANGE_4]->getJsonAttribute('test'), 'test5');

        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_5, $setting);
        $this->assertFalse($setting[SettingsFixture::TYPE_CHANGE_5]->is_confirm);
        $this->assertEquals($setting[SettingsFixture::TYPE_CHANGE_5]->getJsonAttribute('test'), 'test5.5');

        $this->assertArrayHasKey(SettingsFixture::TYPE_CHANGE_6, $setting);
        $this->assertFalse($setting[SettingsFixture::TYPE_CHANGE_6]->is_confirm);
        $this->assertEquals($setting[SettingsFixture::TYPE_CHANGE_6]->getJsonAttribute('test'), 'test10');
    }
}
