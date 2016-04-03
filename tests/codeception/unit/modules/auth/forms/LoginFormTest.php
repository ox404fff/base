<?php

namespace test\modules\auth\forms;

use app\modules\auth\forms\LoginForm;
use test\fixtures\UserFixture;
use yii\codeception\TestCase;

class LoginFormTest extends TestCase
{

    public function fixtures()
    {
        return [
            'user' => UserFixture::className(),
        ];
    }


    protected function tearDown()
    {
        \Yii::$app->user->logout();
        parent::tearDown();
    }

    public function testLoginNoUser()
    {
        $model = new LoginForm([
            'login' => 'not_existing_login',
            'password' => 'not_existing_password',
        ]);

        $this->assertFalse($model->doLogin());
        $this->assertTrue(\Yii::$app->user->getIsGuest());
    }

    public function testLoginWrongPassword()
    {
        $model = new LoginForm([
            'login' => 'user1@email.com',
            'password' => 'wrong_password',
        ]);

        $this->assertFalse($model->doLogin());
        $this->assertArrayHasKey('password', $model->errors);
        $this->assertTrue(\Yii::$app->user->getIsGuest());

    }


    public function testLoginCorrect()
    {

        $model = new LoginForm([
            'login'     => UserFixture::getLogin(UserFixture::ID_USER),
            'password'  => UserFixture::getPassword(UserFixture::ID_USER),
        ]);

        $this->assertTrue($model->doLogin());
        $this->assertArrayNotHasKey('password', $model->errors);
        $this->assertFalse(\Yii::$app->user->getIsGuest());

    }

}
