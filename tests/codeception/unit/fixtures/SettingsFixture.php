<?php

namespace test\fixtures;

use app\modules\cabinet\models\Settings;
use yii\test\ActiveFixture;

class SettingsFixture extends ActiveFixture
{

    const USER_ID = 1;
    const OTHER_USER_ID = 2;
    const OTHER_USER_ID_2 = 3;
    const OTHER_USER_ID_3 = 4;

    const NOT_EXISTS_USER_ID = 99999;

    const USER_ID_PROCESSED_CHANGE = 2;

    const TYPE_CHANGE = 1;
    const TYPE_CHANGE_2 = 2;
    const TYPE_CHANGE_3 = 3;
    const TYPE_CHANGE_4 = 4;
    const TYPE_CHANGE_5 = 5;
    const TYPE_CHANGE_6 = 6;

    public $modelClass = 'app\modules\cabinet\models\Settings';

    /**
     * @param $userId
     * @param $confirmCodeId
     *
     * @return Settings
     */
    public static function createChangePassword($userId, $confirmCodeId)
    {
        $settingsChange = Settings::create($userId, Settings::TYPE_RESET_PASSWORD, [
            'confirm_code_id' => $confirmCodeId
        ], false);
        return $settingsChange;
    }

}