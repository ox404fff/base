<?php

namespace app\modules\cabinet\components;

use app\base\web\Controller as BaseController;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class Controller extends BaseController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['user'],
                    ],
                ],
            ],
        ];
    }


    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ]);
    }


    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $result = parent::beforeAction($action);

        if (\Yii::$app->getUser()->can('user')) {
            $this->setBreadcrumbs($action, \Yii::t('app', 'Cabinet'), ['/cabinet/home/index']);
        }

        return $result;
    }


}
