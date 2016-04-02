<?php

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
