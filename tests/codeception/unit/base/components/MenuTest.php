<?php

namespace test\base\components;

use app\base\components\Menu;
use test\fixtures\UserFixture;
use yii\codeception\TestCase;
use yii\helpers\ArrayHelper;

class MenuTest extends TestCase
{

    public function fixtures()
    {
        return [
            'user' => UserFixture::className(),
        ];
    }


    public function testInit()
    {
        $this->assertTrue(\Yii::$app->has('mainMenu'));

        $this->assertInstanceOf('\yii\web\User', \Yii::$app->mainMenu->user);

        $this->assertArrayHasKey('*', \Yii::$app->mainMenu->items);
        $this->assertArrayHasKey('@', \Yii::$app->mainMenu->items);
        $this->assertArrayHasKey('?', \Yii::$app->mainMenu->items);
        $this->assertArrayHasKey('user', \Yii::$app->mainMenu->items);

    }


    public function validateItems()
    {
        foreach (\Yii::$app->mainMenu->items as $type => $items) {
            foreach ($items as $item) {
                $this->assertArrayHasKey('label', $item);
                $this->assertArrayHasKey('url', $item);
            }
        }
    }


    public function testGetItems()
    {
        \Yii::$app->mainMenu->items = $this->testItems;

        \Yii::$app->user->logout();

        $this->assertEquals(\Yii::$app->mainMenu->getItems(), ArrayHelper::merge(
            $this->testItems['*'],
            $this->testItems['?']
        ));

        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER_INACTIVE);
        \Yii::$app->user->login($user);

        $this->assertEquals(\Yii::$app->mainMenu->getItems(), ArrayHelper::merge(
            $this->testItems['*'],
            $this->testItems['@']
        ));

        \Yii::$app->user->logout();
        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER);
        \Yii::$app->user->login($user);

        $this->assertEquals(\Yii::$app->mainMenu->getItems(), \Yii::$app->mainMenu->replaceShortTags(ArrayHelper::merge(
            $this->testItems['*'],
            $this->testItems['user']
        )));
    }


    public function testReplaceShortTags()
    {
        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER);
        \Yii::$app->user->login($user);

        $menu = new Menu();
        $items = $menu->replaceShortTags($this->testItems['user']);
        $this->assertEquals($items[0]['label'], 'first '.UserFixture::getName(UserFixture::ID_USER).' last');
    }


    public function testReplaceShortTagsUserLogin()
    {
        $menu = new Menu();
        $string = $menu->replaceShortTagsUserLogin('first {#user_login} last');

        $this->assertEquals($string, 'first {#user_login} last');

        $user = $this->getFixture('user')->getModel(UserFixture::ID_USER);
        \Yii::$app->user->login($user);

        $menu = new Menu();
        $string = $menu->replaceShortTagsUserLogin('first {#user_login} last');

        $this->assertEquals($string, 'first '.UserFixture::getLogin(UserFixture::ID_USER).' last');
    }


    public $testItems = [
        'user' => [
            [
                'label' => 'first {#user_login} last',
                'items' => [
                    ['label' => 'cabinet', 'url' => '/cabinet/'],
                    ['label' => 'settings', 'url' => '/cabinet/settings'],
                ]
            ]
        ],
        '@' => [
            [
                'label' => 'Item for unconfirmed users',
                'url' => ['/auth/authorisation/logout']
            ]
        ],
        '?' => [['label' => 'Item for guests', 'url' => ['/auth/authorisation/login']]],
        '*' => [['label' => 'Item for All users', 'url' => ['site/index']]],
    ];

}
