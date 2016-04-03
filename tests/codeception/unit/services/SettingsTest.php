<?php

namespace test\modules\cabinet\services;

use app\models\ConfirmCode;
use app\services\ConfirmCodeService;
use test\fixtures\ConfirmCodeFixture;
use test\fixtures\UserFixture;
use yii\codeception\TestCase;

/**
 * User settings service
 *
 * Class Settings
 * @package app\modules\cabinet\services
 */
class ConfirmCodeServiceTest extends TestCase
{

    protected function setUp()
    {
        parent::setUp();

        \Yii::$app->mailer->fileTransportCallback = function ($mailer, $message) {
            return 'testing_message.eml';
        };
    }


    public function fixtures()
    {
        return [
            'confirm_code' => ConfirmCodeFixture::className(),
            'user' => UserFixture::className(),
        ];
    }

    public function testCreateConfirmEmailCode()
    {
        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER);

        $confirmCodeModel = ConfirmCodeService::createConfirmCode(ConfirmCode::TYPE_CONFIRM_EMAIL, $user->id);

        $this->assertEquals($confirmCodeModel->user_id, $user->id);

        $this->assertEquals(strlen($confirmCodeModel->confirm_code), ConfirmCode::getLength(ConfirmCode::TYPE_CONFIRM_EMAIL));

        $this->assertEquals(strtolower($confirmCodeModel->confirm_code), $confirmCodeModel->confirm_code, 'Confirm email code should be lowercase');

    }

    public function testSendConfirmEmail()
    {

        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER);
        $confirmCodeModel = ConfirmCodeService::createConfirmCode(ConfirmCode::TYPE_CONFIRM_EMAIL, $user->id);
        ConfirmCodeService::sendConfirmEmail($user, $confirmCodeModel->confirm_code);

        $this->assertTrue(file_exists($this->getMessageFile()),
            'Email file should exist');

        $emailMessage = file_get_contents($this->getMessageFile());

        $this->assertContains($user->getLogin(), $emailMessage,
            'email should contain sender email');

        $this->assertContains($confirmCodeModel->confirm_code, $emailMessage,
            'email should contain confirm code');

    }


    public function testSendResetPassword()
    {
        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER);
        $resetCodeModel = ConfirmCodeService::createConfirmCode(ConfirmCode::TYPE_RESET_PASSWORD_EMAIL, $user->id);
        ConfirmCodeService::sendResetPassword($user, $resetCodeModel->confirm_code);

        $this->assertTrue(file_exists($this->getMessageFile()),
            'Email file should exist');

        $emailMessage = file_get_contents($this->getMessageFile());

        $this->assertContains($user->getLogin(), $emailMessage,
            'email should contain sender email');

        $this->assertContains($resetCodeModel->confirm_code, $emailMessage,
            'email should contain reset code');
    }


    private function getMessageFile()
    {
        return \Yii::getAlias(\Yii::$app->mailer->fileTransportPath) . '/testing_message.eml';
    }

} 