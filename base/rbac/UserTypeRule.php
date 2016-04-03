<?php

namespace app\base\rbac;

use app\models\User;
use yii\rbac\Rule;

/**
 * Basic type user rules
 *
 * Class UserTypeRule
 * @package app\rbac
 */
class UserTypeRule extends Rule
{
    public $name = 'userType';

    public function execute($userId, $item, $params)
    {
        $userModel = User::findIdentity($userId);

        if (empty($userModel) || $userModel->isDeleted()) {
            return false;
        }

        if ($item->name == 'user') {
            return in_array($userModel->type, User::$activeUserTypes);
        }

        if ($item->name == 'admin') {
            return in_array($userModel->type, User::$adminUserTypes);
        }

        return true;
    }
} 