<?php

namespace app\modules\cabinet\controllers;

use Yii;
use app\modules\cabinet\components\Controller;

class HomeController extends Controller
{

    public function actionIndex()
    {
        return $this->render('index');
    }

}
