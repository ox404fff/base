<?php
/**
 * @link https://github.com/ox404fff/base/
 * @author ox404fff
 */

namespace app\base\web;

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

}
