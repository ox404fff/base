<?
/**
 * @link https://github.com/ox404fff/base/
 * @author ox404fff
 */

namespace app\base\web;

use app\models\User;
use yii\web\User as BaseUser;

/**
 * Class User
 *
 * @inheritdoc
 *
 * @package app\base\web
 */
class UserWeb extends BaseUser
{

    public function init()
    {
        parent::init();
    }


    /**
     * Returns a login represents the user.
     * @see getIdentity()
     */
    public function getLogin()
    {
        $identity = $this->getIdentity();

        return $identity !== null ? $identity->getLogin() : null;
    }


    /**
     * @inheritdoc
     * @return null|User
     */
    public function getIdentity($autoRenew = true)
    {
        return parent::getIdentity($autoRenew);
    }


    /**
     * Get username
     *
     * @return null|User
     */
    public function getName()
    {
        return $this->getIdentity()->login;
    }

}
