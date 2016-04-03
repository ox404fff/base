<?php

namespace test\models;

use app\models\User;
use test\fixtures\UserFixture;
use yii\codeception\TestCase;

class UserTest extends TestCase
{

    public function fixtures()
    {
        return [
            'user' => UserFixture::className(),
        ];
    }


    public function testFindIdentity()
    {
        $user = User::findIdentity(UserFixture::ID_USER);
        $this->assertFalse(empty($user), 'User should be not empty');
    }


    public function testFindByLogin()
    {
        /**
         * Find normal user by login and any type
         */
        $login = UserFixture::getLogin(UserFixture::ID_USER);

        $user = User::findByLogin($login);
        $this->assertNotEmpty($user);

        /**
         * Find admin user by login and inactive type
         */
        $login = UserFixture::getLogin(UserFixture::ID_USER_INACTIVE);

        $user = User::findByLogin($login, User::TYPE_INACTIVE);
        $this->assertNotEmpty($user);

        /**
         * Find admin user by login and wrong type
         */
        $login = UserFixture::getLogin(UserFixture::ID_USER_INACTIVE);

        $user = User::findByLogin($login, User::TYPE_USER);
        $this->assertEmpty($user);
    }


    public function testIsLoginExists()
    {

        /**
         * Is exists normal user by login and any type
         */
        $login = UserFixture::getLogin(UserFixture::ID_USER);
        $this->assertTrue(User::isLoginExists($login));

        /**
         * Is exists admin user by login and admin type
         */
        $login = UserFixture::getLogin(UserFixture::ID_USER_INACTIVE);
        $this->assertTrue(User::isLoginExists($login, User::TYPE_INACTIVE));

        /**
         * Is exists admin user by login and wrong type
         */
        $login = UserFixture::getLogin(UserFixture::ID_USER_ADMIN);
        $this->assertFalse(User::isLoginExists($login, User::TYPE_USER));
    }


    public function testCreateUser()
    {
        $newUser = User::createUser('newLogin', 'passwordhash');
        $this->assertTrue($newUser->save());
        $this->assertEquals($newUser->type, User::TYPE_INACTIVE);
    }


    public function testGetId()
    {
        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER);
        $this->assertEquals($user->getId(), UserFixture::ID_USER);
    }

    public function testGetEmail()
    {
        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER);
        $this->assertEquals($user->getEmail(), UserFixture::getLogin(UserFixture::ID_USER));
    }


    public function testGetLogin()
    {
        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER);
        $this->assertEquals($user->getLogin(), UserFixture::getLogin(UserFixture::ID_USER));
    }

    public function testGetAuthKey()
    {
        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER);
        $this->assertEquals($user->getAuthKey(), UserFixture::getAuthKey(UserFixture::ID_USER));
    }


    public function testValidateAuthKey()
    {
        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER);
        $this->assertTrue($user->validateAuthKey(UserFixture::getAuthKey(UserFixture::ID_USER)));
        $this->assertFalse($user->validateAuthKey('invalid_auth_key'));

    }


    public function testIsActive()
    {
        $activeUser = $this->getFixture('user')->getModel(UserFixture::ID_USER);
        $this->assertTrue($activeUser->isActive());

        $adminUser = $this->getFixture('user')->getModel(UserFixture::ID_USER_ADMIN);
        $this->assertTrue($adminUser->isActive());

        $inactiveUser = $this->getFixture('user')->getModel(UserFixture::ID_USER_INACTIVE);
        $this->assertFalse($inactiveUser->isActive());

        $deletedUser = $this->getFixture('user')->getModel(UserFixture::ID_USER_DELETED);
        $this->assertEmpty($deletedUser);
    }


    public function testIsAdmin()
    {
        $inactiveUser = $this->getFixture('user')->getModel(UserFixture::ID_USER_INACTIVE);
        $this->assertFalse($inactiveUser->isAdmin());

        $activeUser = $this->getFixture('user')->getModel(UserFixture::ID_USER);
        $this->assertFalse($activeUser->isAdmin());

        $adminUser = $this->getFixture('user')->getModel(UserFixture::ID_USER_ADMIN);
        $this->assertTrue($adminUser->isAdmin());

    }

    public function testIsPasswordSet()
    {
        $inactiveUser = $this->getFixture('user')->getModel(UserFixture::ID_USER_INACTIVE);
        $this->assertTrue($inactiveUser->isPasswordSet());

        $newUser = new User();
        $this->assertFalse($newUser->isPasswordSet());

    }
}
