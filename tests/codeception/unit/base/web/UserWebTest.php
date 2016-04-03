<?php

namespace test\base\web;

use app\base\web\UserWeb;
use test\fixtures\UserFixture;
use yii\codeception\TestCase;

class UserWebTest extends TestCase
{

    public function fixtures()
    {
        return [
            'user' => UserFixture::className(),
        ];
    }


    public function testGetLogin()
    {
        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER);
        $webUser = new UserWeb([
            'identityClass'      => 'app\models\User',
        ]);
        $this->assertTrue($webUser->login($user));

        $this->assertNotEmpty($webUser->getLogin());

        $this->assertTrue(is_string($webUser->getLogin()));
    }


    public function testGetIdentity()
    {
        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER);
        $webUser = new UserWeb([
            'identityClass'      => 'app\models\User',
        ]);
        $this->assertTrue($webUser->login($user));

        $this->assertInstanceOf('\app\models\User', $webUser->getIdentity());
    }


    public function testGetName()
    {
        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER);
        $webUser = new UserWeb([
            'identityClass'      => 'app\models\User',
        ]);
        $this->assertTrue($webUser->login($user));

        $this->assertNotEmpty($webUser->getName());

        $this->assertTrue(is_string($webUser->getName()));
    }


}
