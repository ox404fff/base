<?php

namespace test\modules\auth\forms;

use app\models\ConfirmCode;
use app\modules\auth\forms\ForgotForm;
use app\modules\cabinet\models\Settings;
use test\fixtures\UserFixture;
use yii\codeception\TestCase;

class ForgotFormTest extends TestCase
{

    public function fixtures()
    {
        return [
            'user' => UserFixture::className(),
        ];
    }

    protected function setUp()
    {
        parent::setUp();

        \Yii::$app->mailer->fileTransportCallback = function ($mailer, $message) {
            return md5($this->getTestFullName($this)).'.eml';
        };

        $message = $this->getMessageFile();
        if (file_exists($message)) {
            unlink($message);
        }
    }

    public function testSendNormalUserCode()
    {

        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER);

        $model = new ForgotForm();
        $model->login = $user->login;
        $this->assertTrue($model->doSendResetCode());

        $confirmCode = ConfirmCode::getLastCode(ConfirmCode::TYPE_RESET_PASSWORD_EMAIL, $user->id);

        $this->assertNotEmpty($confirmCode);

        $settingChange = Settings::getLast($user->id, Settings::TYPE_RESET_PASSWORD, false);

        $this->assertNotEmpty($settingChange);

        $this->assertEquals($confirmCode->id, $settingChange->getJsonAttribute('confirm_code_id'));

        $this->assertTrue(file_exists($this->getMessageFile()),
            'Email file should exist');


    }


    public function testSendExistingNotActiveUserCode()
    {
        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER_INACTIVE);

        $model = new ForgotForm();
        $model->login = $user->login;
        $this->assertTrue($model->doSendResetCode());

        $confirmCode = ConfirmCode::getLastCode(ConfirmCode::TYPE_RESET_PASSWORD_EMAIL, $user->id);

        $this->assertNotEmpty($confirmCode);

        $settingChange = Settings::getLast($user->id, Settings::TYPE_RESET_PASSWORD, false);

        $this->assertNotEmpty($settingChange);

        $this->assertEquals($confirmCode->id, $settingChange->getJsonAttribute('confirm_code_id'));

        $this->assertTrue(file_exists($this->getMessageFile()),
            'Email file should exist');
    }


    public function testSendNotExistingUserLogin()
    {

        $model = new ForgotForm();
        $model->login = 'Not_existing_login@exemple.com';
        $this->assertTrue($model->doSendResetCode());

        $this->assertFalse(file_exists($this->getMessageFile()),
            'Email file should exist');
    }


    public function testGetIsUserExists()
    {
        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER_INACTIVE);

        $model = new ForgotForm();
        $model->login = 'Not_existing_login@exemple.com';
        $this->assertFalse($model->getIsUserExists());

        $model->login = $user->login;
        $this->assertFalse($model->getIsUserExists(), 'Is exists user result must be cached');

        $model = new ForgotForm();
        $model->login = $user->login;
        $this->assertTrue($model->getIsUserExists());
    }


    private function getMessageFile()
    {
        return \Yii::getAlias(\Yii::$app->mailer->fileTransportPath) . '/'. md5($this->getTestFullName($this)).'.eml';
    }

}
