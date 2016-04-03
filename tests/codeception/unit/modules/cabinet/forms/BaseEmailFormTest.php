<?php

namespace test\modules\cabinet\forms;

use app\modules\cabinet\forms\BaseEmailForm;
use app\modules\cabinet\models\Settings;
use test\fixtures\SettingsFixture;
use test\fixtures\UserFixture;
use yii\codeception\TestCase;

class BaseEmailFormTest extends TestCase
{

    public function fixtures()
    {
        return [
            'user' => UserFixture::className(),
            'settings' => SettingsFixture::className(),
        ];
    }


    public function testIsEmailConfirmed()
    {
        /**
         * Not confirmed
         */
        $model = new BaseEmailForm();
        $inactiveUser = $this->getFixture('user')->getModel(UserFixture::ID_USER_INACTIVE);
        $settingsChange = Settings::getLast(UserFixture::ID_USER_INACTIVE, Settings::TYPE_CHANGE_EMAIL);

        $this->assertTrue($model->setUser($inactiveUser));
        $this->assertTrue($model->setSettingsChange($settingsChange));
        $this->assertFalse($model->isEmailConfirmed());

        /**
         * Confirmed
         */
        $model = new BaseEmailForm();
        $confirmedUser = $this->getFixture('user')->getModel(UserFixture::ID_USER_CONFIRMED_LOGIN);
        $settingsChange = Settings::getLast(UserFixture::ID_USER_CONFIRMED_LOGIN, Settings::TYPE_CHANGE_EMAIL);

        $this->assertTrue($model->setUser($confirmedUser));
        $this->assertTrue($model->setSettingsChange($settingsChange));
        $this->assertTrue($model->isEmailConfirmed());
    }



    public function testSetSettingsChange()
    {
        $model = new BaseEmailForm();
        $settingsChange = Settings::getLast(UserFixture::ID_USER_INACTIVE, Settings::TYPE_CHANGE_EMAIL);

        /**
         * "clone" for check is changed attributes after set
         */
        $this->assertTrue($model->setSettingsChange(clone $settingsChange));

        $this->assertEquals($model->getSettingsChange()->getAttributes(), $settingsChange->getAttributes());
    }

}
