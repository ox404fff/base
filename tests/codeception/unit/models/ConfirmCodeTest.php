<?php

namespace test\models;

use app\base\helpers\DateTime;
use app\models\ConfirmCode;
use test\fixtures\ConfirmCodeFixture;
use yii\codeception\TestCase;
use yii\helpers\ArrayHelper;

class ConfirmCodeTest extends TestCase
{

    public static $oneDay;
    public static $twoDay;
    public static $threeDay;

    public static $time;

    public function fixtures()
    {
        return [
            'confirm_code' => ConfirmCodeFixture::className(),
        ];
    }

    public function setUp()
    {
        parent::setUp();

        self::$oneDay = DateTime::DAY;
        self::$twoDay = DateTime::DAY * 2;
        self::$threeDay = DateTime::DAY * 3;

        DateTime::$time = time();

        ConfirmCode::$lifeTimeByTypes = ArrayHelper::merge(ConfirmCode::$lifeTimeByTypes, [
            ConfirmCodeFixture::NOT_EXISTS_IN_FIXTURE_TYPE => self::$twoDay
        ]);

        /**
         * Freeze time
         */
    }


    public function testCreateCode()
    {

        $confirmCodeForAllUsers = ConfirmCode::createCode(1, 1);
        $this->assertTrue($confirmCodeForAllUsers->save(),
            'Confirm code for all user should by saved');

        $confirmCodeForOneUser = ConfirmCode::createCode(1, 1, 1);
        $this->assertTrue($confirmCodeForOneUser->save(),
            'Confirm code for one user should by saved');
    }


    public function testCheckCodeAllUsers()
    {

        $result = ConfirmCode::checkCode(
            ConfirmCodeFixture::TYPE,
            ConfirmCodeFixture::CODE_ALL_USERS
        );

        $this->assertTrue($result, 'code for all users should by correct');

        $result = ConfirmCode::checkCode(
            ConfirmCodeFixture::TYPE,
            ConfirmCodeFixture::CODE_ONE_USER
        );

        $this->assertFalse($result, 'code for one user should by invalid');

    }

    public function testCheckCodeOneUser()
    {

        $result = ConfirmCode::checkCode(
            ConfirmCodeFixture::TYPE,
            ConfirmCodeFixture::CODE_ALL_USERS,
            ConfirmCodeFixture::USER_ID
        );

        $this->assertTrue($result, 'code for all users should by correct');

        $result = ConfirmCode::checkCode(
            ConfirmCodeFixture::TYPE,
            ConfirmCodeFixture::CODE_ONE_USER,
            ConfirmCodeFixture::USER_ID
        );

        $this->assertTrue($result, 'code for one user should by correct');

        $result = ConfirmCode::checkCode(
            ConfirmCodeFixture::TYPE,
            ConfirmCodeFixture::CODE_ONE_USER,
            ConfirmCodeFixture::OTHER_USER_ID
        );

        $this->assertFalse($result, 'code for other user should by invalid');

    }


    public function testClearCodesAllUsers()
    {
        $countDeleted = ConfirmCode::clearCodes(ConfirmCodeFixture::TYPE);

        $this->assertNotEquals(0, $countDeleted,
            'Count cleared codes should not be equals zero');

        $result = ConfirmCode::checkCode(
            ConfirmCodeFixture::TYPE,
            ConfirmCodeFixture::CODE_ALL_USERS
        );

        $this->assertFalse($result,
            'Result checking code, after delete should equals false');

    }


    public function testClearCodesOneUser()
    {

        $countDeleted = ConfirmCode::clearCodes(ConfirmCodeFixture::TYPE, ConfirmCodeFixture::USER_ID);

        $this->assertNotEquals(0, $countDeleted,
            'Count cleared codes should not be equals zero');

        $result = ConfirmCode::checkCode(
            ConfirmCodeFixture::TYPE,
            ConfirmCodeFixture::CODE_ONE_USER,
            ConfirmCodeFixture::USER_ID
        );

        $this->assertFalse($result,
            'Result checking code, after delete should equals false'
        );
    }


    public function testGetLength()
    {
        $lengthConfirmEmailCode = ConfirmCode::getLength(ConfirmCode::TYPE_CONFIRM_EMAIL);
        $this->assertTrue(is_numeric($lengthConfirmEmailCode));
        $this->assertEquals($lengthConfirmEmailCode, 32);
    }




    public function testIsCodeExistsOneDay()
    {
        $confirmCode = ConfirmCode::createCode(ConfirmCodeFixture::NOT_EXISTS_IN_FIXTURE_TYPE, 'any_code', ConfirmCodeFixture::USER_ID);

        $confirmCode->created_at = DateTime::time() - self::$oneDay;
        $confirmCode->updated_at = DateTime::time() - self::$oneDay;

        $confirmCode->detachBehavior('timestamp');
        $confirmCode->save();

        $this->assertTrue(ConfirmCode::isCodeExists(ConfirmCodeFixture::NOT_EXISTS_IN_FIXTURE_TYPE, ConfirmCodeFixture::USER_ID));


    }

    public function testIsCodeExistsForTwoDay()
    {
        $confirmCode = ConfirmCode::createCode(ConfirmCodeFixture::NOT_EXISTS_IN_FIXTURE_TYPE, 'any_code', ConfirmCodeFixture::USER_ID);

        $confirmCode->created_at = DateTime::time() - self::$twoDay;
        $confirmCode->updated_at = DateTime::time() - self::$twoDay;

        $confirmCode->detachBehavior('timestamp');
        $confirmCode->save();

        $this->assertTrue(ConfirmCode::isCodeExists(ConfirmCodeFixture::NOT_EXISTS_IN_FIXTURE_TYPE, ConfirmCodeFixture::USER_ID));
    }


    public function testIsCodeNotExists()
    {
        $confirmCode = ConfirmCode::createCode(ConfirmCodeFixture::NOT_EXISTS_IN_FIXTURE_TYPE, 'any_code', ConfirmCodeFixture::USER_ID);

        $confirmCode->created_at = DateTime::time() - self::$threeDay;
        $confirmCode->updated_at = DateTime::time() - self::$threeDay;

        $confirmCode->detachBehavior('timestamp');
        $confirmCode->save();

        $this->assertFalse(ConfirmCode::isCodeExists(ConfirmCodeFixture::NOT_EXISTS_IN_FIXTURE_TYPE, ConfirmCodeFixture::USER_ID));
    }


    public function testGetLastCode()
    {
        $lastConfirmCode = ConfirmCode::getLastCode(ConfirmCodeFixture::TYPE, ConfirmCodeFixture::USER_ID);

        $this->assertNotEmpty($lastConfirmCode);

        $this->assertEquals($lastConfirmCode->confirm_code, ConfirmCodeFixture::CODE_ALL_USERS);

        $confirmCode = ConfirmCode::createCode(ConfirmCodeFixture::TYPE, 'not_existing_confirm_code', ConfirmCodeFixture::USER_ID);
        $confirmCode->save();

        $lastConfirmCode = ConfirmCode::getLastCode(ConfirmCodeFixture::TYPE, ConfirmCodeFixture::USER_ID);

        $this->assertNotEmpty($lastConfirmCode);

        $this->assertEquals($lastConfirmCode->confirm_code, 'not_existing_confirm_code');
    }


    public function testFindByCode()
    {
        $confirmCode = ConfirmCode::createCode(ConfirmCodeFixture::TYPE, 'test_find_byCode1', ConfirmCodeFixture::USER_ID);
        $this->assertTrue($confirmCode->save());

        $confirmCode = ConfirmCode::findByCode(ConfirmCodeFixture::TYPE, 'test_find_byCode1', ConfirmCodeFixture::USER_ID);
        $this->assertNotEmpty($confirmCode);

        $confirmCode = ConfirmCode::findByCode(ConfirmCodeFixture::TYPE, 'test_find_byCode1');
        $this->assertNotEmpty($confirmCode);
        $this->assertEquals($confirmCode->user_id, ConfirmCodeFixture::USER_ID);

        $confirmCode = ConfirmCode::findByCode(ConfirmCodeFixture::TYPE, 'not_existing_confirm_code1');
        $this->assertEmpty($confirmCode);
    }

}
