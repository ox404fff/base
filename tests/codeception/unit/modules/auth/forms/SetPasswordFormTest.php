<?php

namespace test\modules\auth\forms;

use app\models\ConfirmCode;
use app\modules\auth\forms\SetPasswordForm;
use app\modules\cabinet\models\Settings;
use test\fixtures\ConfirmCodeFixture;
use test\fixtures\SettingsFixture;
use test\fixtures\UserFixture;
use yii\codeception\TestCase;

class SetPasswordFormTest extends TestCase
{

    public function fixtures()
    {
        return [
            'user' => UserFixture::className(),
            'confirm_code' => ConfirmCodeFixture::className(),
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


    public function testInitByCode()
    {
        $confirmCode = $this->getFixture('confirm_code')->getModel(ConfirmCodeFixture::ID_6);
        SettingsFixture::createChangePassword(UserFixture::ID_USER, $confirmCode->id)->save();

        $model = new SetPasswordForm();
        $model->code = ConfirmCodeFixture::getConfirmCode(UserFixture::ID_USER);

        $model->initByCode();

        $this->assertNotEmpty($model->confirmCode);
        $this->assertNotEmpty($model->settingsChange);
        $this->assertNotEmpty($model->getUser());
    }


    public function testValidateCode()
    {
        $confirmCode = $this->getFixture('confirm_code')->getModel(ConfirmCodeFixture::ID_6);
        $changePassword = SettingsFixture::createChangePassword(UserFixture::ID_USER, $confirmCode->id);
        $changePassword->save();

        $model = new SetPasswordForm();
        $model->code = ConfirmCodeFixture::getConfirmCode(UserFixture::ID_USER);
        $model->password = 'polkilo';

        $model->initByCode();
        $this->assertTrue($model->validate());
        $this->assertArrayNotHasKey('code', $model->getErrors());

        $changePassword->setJsonAttribute('confirm_code_id', 9999);
        $changePassword->save();

        $model = new SetPasswordForm();
        $model->code = 'invalid_code';

        $this->assertFalse($model->validateCode('code'));
        $this->assertArrayHasKey('code', $model->getErrors());
    }


    public function testDoSetPassword()
    {
        $confirmCode = $this->getFixture('confirm_code')->getModel(ConfirmCodeFixture::ID_6);
        /**
         * @var ConfirmCode $confirmCode
         */
        $changePassword = SettingsFixture::createChangePassword(UserFixture::ID_USER, $confirmCode->id);
        $changePassword->save();

        $model = new SetPasswordForm();
        $model->code = ConfirmCodeFixture::getConfirmCode(UserFixture::ID_USER);
        $model->password = 'polkilo';

        $model->initByCode();

        $this->assertTrue($model->doSetPassword());

        $changePassword = Settings::findOne(['id' => $changePassword->id]);
        $this->assertTrue($changePassword->is_confirm);

        $confirmCode = ConfirmCode::findOne(['id' => $confirmCode->id]);
        $this->assertEmpty($confirmCode);

        $lastChangePassword = Settings::getLast(UserFixture::ID_USER, Settings::TYPE_CHANGE_PASSWORD);
        $this->assertNotEmpty($lastChangePassword);
        $this->assertTrue($lastChangePassword->is_confirm);
    }


    private function getMessageFile()
    {
        return \Yii::getAlias(\Yii::$app->mailer->fileTransportPath) . '/'. md5($this->getTestFullName($this)).'.eml';
    }

}
