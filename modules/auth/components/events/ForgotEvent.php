<?php
/**
 * Created by PhpStorm.
 * User: ox404fff
 * Date: 14.03.16
 * Time: 11:10
 */

namespace app\modules\auth\components\events;


use app\models\ConfirmCode;
use app\models\User;
use yii\base\Event;

class ForgotEvent extends Event
{

    /**
     * @var User
     */
    public $user;

    /**
     * @var ConfirmCode
     */
    public $confirmCode;


} 