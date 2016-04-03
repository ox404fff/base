<?
/**
 * @link https://github.com/ox404fff/base/
 * @author ox404fff
 */

namespace app\base\web;

use yii\base\Action;
use yii\web\Controller as BaseController;

class Controller extends BaseController
{

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }


    /**
     * Set breadcrumbs
     *
     * @param Action $action
     * @param string $title
     * @param string $path
     */
    public function setBreadcrumbs(Action $action, $title, $path = null)
    {
        $actionRoute = '/'.$action->getUniqueId();

        if ($path == $actionRoute || (is_array($path) && $path[0] == $actionRoute)) {
            $this->getView()->params['breadcrumbs'][] = $title;
        } else {
            $item = [
                'label' => $title,
            ];

            if (!is_null($path)) {
                $item['url'] = $path;
            }

            $this->getView()->params['breadcrumbs'][] = $item;

        }
    }

}
