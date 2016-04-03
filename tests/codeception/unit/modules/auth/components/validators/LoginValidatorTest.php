<?php

namespace test\modules\auth\components\validators;

use app\models\User;
use yii\codeception\TestCase;
use yii\validators\Validator;

class LoginValidatorTest extends TestCase
{

    public function testValidateValueIsEmail()
    {
        $model = new User();

        $validator = Validator::createValidator(
            'app\modules\auth\components\validators\LoginValidator',
            $model, []
        );

        $this->assertInstanceOf('yii\validators\EmailValidator', $validator);
    }

}
