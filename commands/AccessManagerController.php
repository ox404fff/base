<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use \app\base\rbac\UserTypeRule;

/**
 * Init RBAC Roles
 *
 * Class AccessManagerController
 * @package app\commands
 */
class AccessManagerController extends Controller
{

    /**
     * Init roles
     */
    public function actionInit()
    {
        $auth = \Yii::$app->getAuthManager();
        $auth->removeAll();

        $rule = new UserTypeRule();
        $auth->add($rule);

        $user = $auth->createRole('user');
        $user->description = 'Active user';
        $user->ruleName = $rule->name;
        $auth->add($user);

        $admin = $auth->createRole('admin');
        $admin->description = 'Administrator';
        $admin->ruleName = $rule->name;
        $auth->add($admin);
        $auth->addChild($admin, $user);
    }

}
