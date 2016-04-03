<?php

namespace test\modules\cabinet\forms;

use app\models\ConfirmCode;
use app\models\User;
use app\modules\auth\components\validators\LoginExistValidator;
use app\modules\cabinet\forms\ConfirmEmailForm;
use app\modules\cabinet\models\Settings;
use test\fixtures\ConfirmCodeFixture;
use test\fixtures\SettingsFixture;
use test\fixtures\UserFixture;
use yii\codeception\TestCase;

class ConfirmEmailFormTest extends TestCase
{

    public function fixtures()
    {
        return [
            'user' => UserFixture::className(),
            'confirm_codes' => ConfirmCodeFixture::className(),
            'settings' => SettingsFixture::className(),
        ];
    }


    public function testRules()
    {
        $model = new ConfirmEmailForm();

        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER);
        $model->setUser($user);

        $searchLoginExistsValidator = false;
        foreach ($model->getActiveValidators('email') as $key => $validator) {
            if ($validator->className() == LoginExistValidator::className()) {
                $searchLoginExistsValidator = true;
            }
        }
        $this->assertTrue($searchLoginExistsValidator);
    }


    public function testValidateEmail()
    {
        /**
         * Invalid
         */
        $model = new ConfirmEmailForm();

        $confirmedUser = $this->getFixture('user')->getModel(UserFixture::ID_USER_CONFIRMED_LOGIN);

        $this->assertTrue($model->setUser($confirmedUser));

        $settingsChange = Settings::getLast(UserFixture::ID_USER_CONFIRMED_LOGIN, Settings::TYPE_CHANGE_EMAIL);

        $this->assertTrue($model->setSettingsChange($settingsChange));

        $model->validateEmail('email');
        $this->assertArrayHasKey('email', $model->errors);

        /**
         * Valid
         */
        $model = new ConfirmEmailForm();

        $inactiveUser = $this->getFixture('user')->getModel(UserFixture::ID_USER_INACTIVE);

        $this->assertTrue($model->setUser($inactiveUser));

        $settingsChange = Settings::getLast(UserFixture::ID_USER_INACTIVE, Settings::TYPE_CHANGE_EMAIL);

        $this->assertTrue($model->setSettingsChange($settingsChange));

        $model->validateEmail('email');
        $this->assertArrayNotHasKey('email', $model->errors);
    }

    public function testValidateCode()
    {
        /**
         * Invalid
         */
        $model = new ConfirmEmailForm();
        $inactiveUser = $this->getFixture('user')->getModel(UserFixture::ID_USER_INACTIVE);
        $this->assertTrue($model->setUser($inactiveUser));

        $settingsChange = Settings::getLast(UserFixture::ID_USER_INACTIVE, Settings::TYPE_CHANGE_EMAIL);

        $this->assertTrue($model->setSettingsChange($settingsChange));

        $model->code =  str_repeat('i', ConfirmCode::getLength(ConfirmCode::TYPE_CONFIRM_EMAIL));

        $model->validateCode('code');
        $this->assertArrayHasKey('code', $model->errors);

        /**
         * Valid
         */
        $model = new ConfirmEmailForm();
        $inactiveUser = $this->getFixture('user')->getModel(UserFixture::ID_USER_INACTIVE);
        $this->assertTrue($model->setUser($inactiveUser));

        $settingsChange = Settings::getLast(UserFixture::ID_USER_INACTIVE, Settings::TYPE_CHANGE_EMAIL);

        $this->assertTrue($model->setSettingsChange($settingsChange));

        $model->code =  ConfirmCodeFixture::getConfirmCode(UserFixture::ID_USER_INACTIVE);

        $model->validateCode('code');
        $this->assertArrayNotHasKey('code', $model->errors);
    }


    public function testCorrectConfirmEmailCodeNewUser()
    {
        $model = new ConfirmEmailForm();
        $inactiveUser = $this->getFixture('user')->getModel(UserFixture::ID_USER_INACTIVE);
        /**
         * @var User $inactiveUser
         */
        $model->setUser($inactiveUser);

        $settingsChange = Settings::getLast(UserFixture::ID_USER_INACTIVE, Settings::TYPE_CHANGE_EMAIL);

        $this->assertTrue($model->setSettingsChange($settingsChange));

        $model->code =  PHP_EOL.ConfirmCodeFixture::getConfirmCode(UserFixture::ID_USER_INACTIVE)."  \n\r";

        $this->assertTrue($model->doConfirm());

        $settingsChange = Settings::getLast(UserFixture::ID_USER_INACTIVE, Settings::TYPE_CHANGE_EMAIL);
        $this->assertTrue($settingsChange->is_confirm);

        $this->assertTrue($inactiveUser->isActive());

    }


    public function testCorrectConfirmEmailCodeNormalUser()
    {
        $model = new ConfirmEmailForm();
        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER);
        /**
         * @var User $user
         */
        $model->setUser($user);

        $settingsChange = Settings::getLast(UserFixture::ID_USER, Settings::TYPE_CHANGE_EMAIL);

        $this->assertTrue($model->setSettingsChange($settingsChange));

        $model->code =  PHP_EOL.ConfirmCodeFixture::getConfirmCode(UserFixture::ID_USER)."  \n\r";

        $this->assertTrue($model->doConfirm());

        $settingsChange = Settings::getLast(UserFixture::ID_USER, Settings::TYPE_CHANGE_EMAIL);
        $this->assertTrue($settingsChange->is_confirm);

        $this->assertEquals($user->login, 'new_changed_email@exemple.com');


        /**
         * Confirm code should be removed after success activation
         */
        $this->assertFalse(ConfirmCode::checkCode(ConfirmCode::TYPE_CONFIRM_EMAIL, $model->code, $user->id));
    }


    public function testCorrectConfirmEmailExistsUser()
    {

        $model = new ConfirmEmailForm();
        $inactiveUser = $this->getFixture('user')->getModel(UserFixture::ID_USER_INACTIVE);
        /**
         * @var User $inactiveUser
         */
        $model->setUser($inactiveUser);

        $settingsChange = Settings::getLast(UserFixture::ID_USER_INACTIVE, Settings::TYPE_CHANGE_EMAIL);

        /** Set exist user login */
        $settingsChange->setJsonAttribute('email', UserFixture::getLogin(UserFixture::ID_USER));

        $this->assertTrue($model->setSettingsChange($settingsChange));

        $model->code =  PHP_EOL.ConfirmCodeFixture::getConfirmCode(UserFixture::ID_USER_INACTIVE)."  \n\r";

        $this->assertFalse($model->doConfirm());

        $this->assertArrayHasKey('email', $model->errors);

    }


}
