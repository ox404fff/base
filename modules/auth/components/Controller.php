<?php

namespace app\modules\auth\components;

use app\base\web\Controller as BaseController;
use yii\helpers\ArrayHelper;

class Controller extends BaseController
{

    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ]);
    }

}
