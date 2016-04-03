<?php

namespace test\fixtures;

use app\models\ConfirmCode;
use yii\test\ActiveFixture;

class ConfirmCodeFixture extends ActiveFixture
{

    const CODE_ONE_USER = 'test_confirm_code_one_user';

    const CODE_ALL_USERS = 'test_confirm_code_all_users';

    const TYPE = 1;

    const NOT_EXISTS_IN_FIXTURE_TYPE = 100;

    const USER_ID = 10;

    const OTHER_USER_ID = 2;

    /**
     * Fixture records ids
     */
    const ID_1 = 1;
    const ID_2 = 2;
    const ID_3 = 3;
    const ID_4 = 4;
    const ID_5 = 5;
    const ID_6 = 6;

    public $modelClass = 'app\models\ConfirmCode';

    /**
     * @var
     */
    private static $confirmCodes = array();

    /**
     * @param $userId
     */
    public static function createConfirmCode($userId)
    {
        self::$confirmCodes[$userId] = str_repeat('v', ConfirmCode::getLength(ConfirmCode::TYPE_CONFIRM_EMAIL));
        return self::$confirmCodes[$userId];
    }

    /**
     * @param $userId
     * @return bool
     */
    public static function getConfirmCode($userId)
    {
        return isset(self::$confirmCodes[$userId]) ? self::$confirmCodes[$userId] : false;
    }


}