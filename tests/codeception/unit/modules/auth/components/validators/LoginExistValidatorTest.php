<?php

namespace test\modules\auth\components\validators;

use app\models\User;
use test\fixtures\UserFixture;
use yii\codeception\TestCase;
use yii\validators\Validator;

class LoginExistValidatorTest extends TestCase
{

    public function fixtures()
    {
        return [
            'user' => UserFixture::className(),
        ];
    }

    public function testValidateValueExistsActiveUserLogin()
    {
        $model = new User();

        $validator = Validator::createValidator(
            'app\modules\auth\components\validators\LoginExistValidator',
            $model, []
        );

        $login = UserFixture::getLogin(UserFixture::ID_USER);

        $this->assertFalse($validator->validate($login, $error));
        $this->assertNotNull($error);

        $login = UserFixture::getLogin(UserFixture::ID_USER_ADMIN);

        $this->assertFalse($validator->validate($login, $error));
        $this->assertNotNull($error);
    }


    public function testValidateValueExistsInActiveLogin()
    {
        $model = new User();

        $validator = Validator::createValidator(
            'app\modules\auth\components\validators\LoginExistValidator',
            $model, []
        );

        $login = UserFixture::getLogin(UserFixture::ID_USER_INACTIVE);

        $this->assertTrue($validator->validate($login, $error));
        $this->assertNull($error);

        $login = UserFixture::getLogin(UserFixture::ID_USER_DELETED);

        $this->assertTrue($validator->validate($login, $error));
        $this->assertNull($error);
    }


    public function testValidateValueNotExistsLogin()
    {
        $model = new User();

        $validator = Validator::createValidator(
            'app\modules\auth\components\validators\LoginExistValidator',
            $model, []
        );

        $login = UserFixture::getLogin(UserFixture::ID_NOT_EXISTS_USER);

        $this->assertTrue($validator->validate($login, $error));
        $this->assertNull($error);
    }
}
