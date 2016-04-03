<?php

namespace app\modules\auth\controllers;

use app\modules\auth\forms\ForgotForm;
use app\modules\auth\forms\LoginForm;
use app\modules\auth\forms\SetPasswordForm;
use Yii;
use app\modules\auth\components\Controller;

class ForgotController extends Controller
{

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $result = parent::beforeAction($action);

        if ($action->id == 'index') {
            $this->setBreadcrumbs($action, \Yii::t('app', 'Password reminder'));
        } else {
            $this->setBreadcrumbs($action, \Yii::t('app', 'Password reminder'), ['/auth/forgot/index']);
        }

        return $result;
    }


    public function actionIndex()
    {
        $model = new ForgotForm();

        if (!\Yii::$app->getUser()->getIsGuest()) {
            $model->login = \Yii::$app->getUser()->getLogin();
        }

        $isCodeSend = false;
        if ($model->load(Yii::$app->request->post()) && $model->doSendResetCode()) {
            $isCodeSend = true;
        }

        return $this->render('index', [
            'model'      => $model,
            'isCodeSend' => $isCodeSend
        ]);
    }


    public function actionSetPassword($code = null)
    {
        $model = new SetPasswordForm();

        if (!is_null($code)) {
            $model->code = $code;
        }

        $isPasswordSet = false;
        if ($model->load(Yii::$app->request->post())) {

            $model->initByCode();

            if ($model->doSetPassword()) {
                $isPasswordSet = true;

                if (Yii::$app->user->getIsGuest()) {
                    Yii::$app->user->login($model->getUser());
                }

            }
        }

        return $this->render('setPassword', [
            'model'         => $model,
            'isPasswordSet' => $isPasswordSet
        ]);
    }

}
