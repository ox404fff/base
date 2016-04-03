<?php

namespace test\modules\cabinet\forms;

use app\models\ConfirmCode;
use app\models\User;
use app\modules\auth\components\validators\LoginExistValidator;
use app\modules\cabinet\forms\ChangeEmailForm;
use app\modules\cabinet\models\Settings;
use test\fixtures\ConfirmCodeFixture;
use test\fixtures\SettingsFixture;
use test\fixtures\UserFixture;
use yii\codeception\TestCase;

class ChangeEmailFormTest extends TestCase
{

    public function fixtures()
    {
        return [
            'user' => UserFixture::className(),
            'confirm_codes' => ConfirmCodeFixture::className(),
            'settings' => SettingsFixture::className(),
        ];
    }

    protected function setUp()
    {
        parent::setUp();

        $message = $this->getMessageFile();
        if (file_exists($message)) {
            unlink($message);
        }

        \Yii::$app->mailer->fileTransportCallback = function ($mailer, $message) {
            return md5($this->getTestFullName($this)).'.eml';
        };
    }


    public function testRules()
    {
        $model = new ChangeEmailForm();
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


    public function testDoChange()
    {
        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER);

        $this->assertTrue(ConfirmCode::checkCode(
            ConfirmCode::TYPE_CONFIRM_EMAIL,
            ConfirmCodeFixture::getConfirmCode(UserFixture::ID_USER),
            $user->id
        ));

        $model = new ChangeEmailForm();
        $model->setUser($user);
        $settingsChange = Settings::getLast($user->getId(), Settings::TYPE_CHANGE_EMAIL);
        $model->setSettingsChange($settingsChange);

        $model->email = 'new_email_for_user@exemple.com';

        $this->assertTrue($model->doChange());

        $this->assertFalse(ConfirmCode::checkCode(
            ConfirmCode::TYPE_CONFIRM_EMAIL,
            ConfirmCodeFixture::getConfirmCode(UserFixture::ID_USER),
            $user->id
        ));

        $settingsChange = Settings::getLast($user->getId(), Settings::TYPE_CHANGE_EMAIL);
        $this->assertEquals($settingsChange->getJsonAttribute('email'), 'new_email_for_user@exemple.com');
        $this->assertFalse($settingsChange->is_confirm);

        $this->assertTrue(file_exists($this->getMessageFile()),
            'Email file should exist');


        $lastCheckCode = ConfirmCode::getLastCode(Settings::TYPE_CHANGE_EMAIL, $user->getId());

        $emailMessage = file_get_contents($this->getMessageFile());

        $this->assertContains($user->getLogin(), $emailMessage,
            'email should contain sender email');

        $this->assertContains($lastCheckCode->confirm_code, $emailMessage,
            'email should contain confirm code');
    }


    public function testGetIsNotEqualsOldEmailActiveUser()
    {
        $model = new ChangeEmailForm();

        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER_2);
        /**
         * @var User $user
         */
        $model->setUser($user);

        $settingsChange = Settings::getLast($user->getId(), Settings::TYPE_CHANGE_EMAIL);

        $this->assertNotEmpty($settingsChange);

        $model->setSettingsChange($settingsChange);

        $model->email = $user->login;

        $this->assertTrue($model->doChange());

        $this->assertFalse($model->getIsNotEqualsOldEmail());

        $this->assertFalse(file_exists($this->getMessageFile()),
            'Email file should exist');

        $settingsChange = Settings::getLast($user->getId(), Settings::TYPE_CHANGE_EMAIL);
        $this->assertTrue($settingsChange->is_confirm);

    }


    public function testGetIsNotEqualsOldEmailInactiveUser()
    {
        $model = new ChangeEmailForm();

        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER_INACTIVE);
        /**
         * @var User $user
         */
        $model->setUser($user);

        $settingsChange = Settings::getLast($user->getId(), Settings::TYPE_CHANGE_EMAIL);

        $model->setSettingsChange($settingsChange);

        $model->email = $user->login;

        $this->assertTrue($model->doChange());

        $settingsChange = Settings::getLast($user->getId(), Settings::TYPE_CHANGE_EMAIL);
        $this->assertEquals($settingsChange->getJsonAttribute('email'), $user->login);
        $this->assertFalse($settingsChange->is_confirm);

        $this->assertTrue(file_exists($this->getMessageFile()),
            'Email file should exist');

        $user = User::findOne(['id' => $user->id]);

        $this->assertFalse($user->isActive());

    }



    private function getMessageFile()
    {
        return \Yii::getAlias(\Yii::$app->mailer->fileTransportPath) . '/'. md5($this->getTestFullName($this)).'.eml';
    }

}
