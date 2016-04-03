<?php

namespace test\modules\auth\forms;

use app\modules\auth\forms\RegistrationForm;
use app\models\ConfirmCode;
use app\modules\cabinet\models\Settings;
use test\fixtures\UserFixture;
use yii\codeception\TestCase;

class RegistrationFormTest extends TestCase
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


    protected function tearDown()
    {
        \Yii::$app->user->logout();
        parent::tearDown();
    }


    public function testAdminUserIsExists()
    {

        $adminUserLogin = UserFixture::getLogin(UserFixture::ID_USER_ADMIN);

        $model = new RegistrationForm([
            'login'    => $adminUserLogin,
            'password' => 'test password'
        ]);

        $this->assertFalse($model->doRegistration());
        $this->assertArrayHasKey('login', $model->errors);
        $this->assertTrue(\Yii::$app->user->getIsGuest());

    }


    public function testActiveUserIsExists()
    {

        $activeUserLogin = UserFixture::getLogin(UserFixture::ID_USER);

        $model = new RegistrationForm([
            'login'    => $activeUserLogin,
            'password' => 'test password'
        ]);

        $this->assertFalse($model->doRegistration());
        $this->assertArrayHasKey('login', $model->errors);
        $this->assertTrue(\Yii::$app->user->getIsGuest());

    }


    public function testInactiveUserIsExists()
    {
        $inactiveUserLogin = UserFixture::getLogin(UserFixture::ID_USER_INACTIVE);

        $model = new RegistrationForm([
            'login'    => $inactiveUserLogin,
            'password' => 'test password'
        ]);

        $this->assertTrue($model->doRegistration());
    }


    public function testNormalRegistration()
    {

        $notExistsUserLogin = 'not_exists_login@mail.com';

        $model = new RegistrationForm([
            'login'    => $notExistsUserLogin,
            'password' => 'test password'
        ]);

        $this->assertTrue($model->doRegistration(),
            'Registration method should return true');

        $confirmCode = ConfirmCode::getLastCode(ConfirmCode::TYPE_CONFIRM_EMAIL, $model->getUser()->getId());

        $this->assertNotEmpty($confirmCode,
            'Confirm code must by created');

        $emailSettingChange = Settings::getLast($model->getUser()->getId(), Settings::TYPE_CHANGE_EMAIL, false);

        $this->assertNotEmpty($emailSettingChange,
            'Email setting change must by created');

        $this->assertEquals(
            $emailSettingChange->getJsonAttribute('email'),
            $model->getUser()->getLogin()
        );

        $passwordSettingChange = Settings::getLast($model->getUser()->getId(), Settings::TYPE_CHANGE_PASSWORD, true);

        $this->assertNotEmpty($passwordSettingChange,
            'Password setting change must by created');


        $this->assertTrue(file_exists($this->getMessageFile()),
            'Email file should exist');


        $lastCheckCode = ConfirmCode::getLastCode(Settings::TYPE_CHANGE_EMAIL, $model->getUser()->getId());

        $emailMessage = file_get_contents($this->getMessageFile());

        $this->assertContains($model->getUser()->getLogin(), $emailMessage,
            'email should contain sender email');

        $this->assertContains($lastCheckCode->confirm_code, $emailMessage,
            'email should contain confirm code');
    }


    private function getMessageFile()
    {
        return \Yii::getAlias(\Yii::$app->mailer->fileTransportPath) . '/'. md5($this->getTestFullName($this)).'.eml';
    }

}
