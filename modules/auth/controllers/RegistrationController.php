<?php

namespace app\modules\auth\controllers;

use app\modules\auth\forms\RegistrationForm;
use Yii;
use app\modules\auth\components\Controller;
use yii\helpers\Url;

class RegistrationController extends Controller
{

    public function actionIndex()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new RegistrationForm();

        if ($model->load(Yii::$app->request->post()) && $model->doRegistration()) {

            Yii::$app->user->login($model->getUser());

            return $this->redirect(
                Url::to(['/cabinet/settings/confirm-email'])
            );
        }

        return $this->render('index', [
            'model' => $model
        ]);
    }


}
