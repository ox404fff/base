<?php

namespace test\modules\cabinet\forms;

use app\models\User;
use app\modules\cabinet\forms\ChangePasswordForm;
use app\modules\cabinet\models\Settings;
use test\fixtures\UserFixture;
use yii\codeception\TestCase;

class ChangePasswordFormTest extends TestCase
{

    public function fixtures()
    {
        return [
            'user' => UserFixture::className(),
        ];
    }


    public function testValidatePassword()
    {

        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER);

        $model = new ChangePasswordForm();
        $model->setUser($user);
        $model->password = 'wrong_password'.UserFixture::getPassword(UserFixture::ID_USER);
        $model->validatePassword('password');
        $this->assertArrayHasKey('password', $model->getErrors());

        $model = new ChangePasswordForm();
        $model->setUser($user);
        $model->password = UserFixture::getPassword(UserFixture::ID_USER);
        $model->validatePassword('password');
        $this->assertArrayNotHasKey('password', $model->getErrors());

    }


    public function testDoChange()
    {
        $newPassword = 'new-password';

        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER);

        $model = new ChangePasswordForm();

        $model->setUser($user);

        $model->password = UserFixture::getPassword(UserFixture::ID_USER);

        $model->newPassword = $newPassword;

        $this->assertTrue($model->doChange());

        $user = User::findIdentity(UserFixture::ID_USER);

        $this->assertTrue(\Yii::$app->getSecurity()->validatePassword($newPassword, $user->password));

        $settingsChange = Settings::getLast($user->id, Settings::TYPE_CHANGE_PASSWORD, true);
        $this->assertNotEmpty($settingsChange);

        $this->assertEmpty($model->password);
        $this->assertEmpty($model->newPassword);

    }

}
