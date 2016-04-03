<?
/**
 * @link https://github.com/ox404fff/base/
 * @author ox404fff
 */

namespace app\base\components;


use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\web\User;

/**
 * Class Menu
 *
 * @package app\base\components
 */
class Menu extends Component
{


    /**
     * All menu items
     *
     * @var array
     */
    public $items = array();


    /**
     * User to return menu
     *
     * @var User
     */
    public $user;


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->items = ArrayHelper::merge($this->items, ['?' => [], '@' => [], '*' => [], 'user' => []]);

        if (is_null($this->user)) {
            $this->user = \Yii::$app->getUser();
        }

        parent::init();
    }


    /**
     * Setting user
     *
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }


    /**
     * Get menu items for user
     */
    public function getItems()
    {
        $result = $this->items['*'];

        if ($this->user->can('user')) {

            $result = ArrayHelper::merge($result, $this->items['user']);

        } elseif (!$this->user->getIsGuest()) {

            $result = ArrayHelper::merge($result, $this->items['@']);

        } else {

            $result = ArrayHelper::merge($result, $this->items['?']);

        }

        $result = $this->replaceShortTags($result);

        return $result;

    }


    /**
     * Replace short tags in menu
     *
     * @param $array
     * @param string $recursion
     * @return mixed
     */
    public function replaceShortTags($array, $recursion = 'items')
    {
        foreach ($array as $key => $item) {

            $array[$key]['label'] = $this->replaceShortTagsUserLogin($array[$key]['label']);

            if (isset($item['items'])) {
                $array[$key]['items'] = $this->replaceShortTags($item['items'], $recursion);
            }

        }

        return $array;
    }


    /**
     * Replace username string
     *
     * @param $string
     * @return mixed
     */
    public function replaceShortTagsUserLogin($string)
    {
        if (\Yii::$app->user->getIsGuest()) {
            return $string;
        }
        return str_replace('{#user_login}', \Yii::$app->user->getLogin(), $string);
    }

}