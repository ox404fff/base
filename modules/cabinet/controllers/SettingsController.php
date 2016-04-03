<?php

namespace app\modules\cabinet\controllers;

use app\modules\cabinet\forms\ChangeEmailForm;
use app\modules\cabinet\forms\ChangePasswordForm;
use app\modules\cabinet\forms\ConfirmEmailForm;
use app\modules\cabinet\components\Controller;
use app\modules\cabinet\models\Settings;
use yii\base\Exception;
use yii\filters\AccessControl;
use Yii;
use \yii\helpers\Url;
use yii\helpers\ArrayHelper;

class SettingsController extends Controller
{

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['confirm-email', 'change-email'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['cancel-changing-email'],
                        'allow' => true,
                        'roles' => ['user'],
                    ],
                ],
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
            $this->setBreadcrumbs($action, \Yii::t('app', 'Settings'), ['/cabinet/settings/index']);
        }

        return $result;
    }


    public function actionIndex()
    {
        $settings = Settings::getAll(\Yii::$app->getUser()->getId());

        $menu = [
            $this->_getEmailSettingsMenuItem($settings),
            $this->_getPasswordSettingsMenuItem($settings),
        ];

        return $this->render('index', [
            'menu' => $menu
        ]);
    }


    /**
     * @param Settings[] $settings
     * @return array
     */
    private function _getPasswordSettingsMenuItem($settings)
    {
        $ico = 'glyphicon-exclamation-sign';
        $tooltip = 'Need set password';

        if (isset($settings[Settings::TYPE_RESET_PASSWORD]) && !$settings[Settings::TYPE_RESET_PASSWORD]->is_confirm) {
            $ico = 'glyphicon-time';
            $tooltip = 'On email sent instructions for reset password';
        } elseif (isset($settings[Settings::TYPE_CHANGE_PASSWORD]) && $settings[Settings::TYPE_CHANGE_PASSWORD]->is_confirm) {
            $ico = 'glyphicon-ok';
            $tooltip = 'Password is successfully set';
        }

        return [
            'label'   => 'Password',
            'route'   => ['/cabinet/settings/change-password'],
            'ico'     => $ico,
            'tooltip' => $tooltip,
        ];
    }


    /**
     * @param Settings[] $settings
     * @return array
     */
    private function _getEmailSettingsMenuItem($settings)
    {
        $ico = 'glyphicon-exclamation-sign';
        $tooltip = 'Need set email';
        $route = ['/cabinet/settings/change-email'];

        if (isset($settings[Settings::TYPE_CHANGE_EMAIL]) && $settings[Settings::TYPE_CHANGE_EMAIL]->is_confirm) {
            $ico = 'glyphicon-ok';
            $tooltip = 'Email is confirmed';
        } elseif (isset($settings[Settings::TYPE_CHANGE_EMAIL]) && !$settings[Settings::TYPE_CHANGE_EMAIL]->is_confirm) {
            $ico = 'glyphicon-time';
            $route = ['/cabinet/settings/confirm-email'];
            $tooltip = 'Waiting for confirmation of a new email';
        }

        return [
            'label'   => 'E-mail \ Login',
            'route'   => $route,
            'ico'     => $ico,
            'tooltip' => $tooltip,
        ];
    }


    public function actionConfirmEmail($code = null)
    {
        $model = new ConfirmEmailForm();

        $model->setUser(\Yii::$app->user->getIdentity());

        $settingsChange = Settings::getLast(\Yii::$app->user->getId(), Settings::TYPE_CHANGE_EMAIL);

        if (empty($settingsChange) || $settingsChange->is_confirm) {
            return $this->redirect(Url::to(['/cabinet/settings/change-email']));
        }

        $model->setSettingsChange($settingsChange);

        $justConfirmed = false;

        if (!is_null($code)) {
            $model->code = $code;
            if ($model->doConfirm()) {
                $justConfirmed = true;
            }
        } else {
            if ($model->load(Yii::$app->request->post()) && $model->doConfirm()) {
                $justConfirmed = true;
            }
        }

        return $this->render('confirmEmail', [
            'model'         => $model,
            'justConfirmed' => $justConfirmed
        ]);
    }


    public function actionChangeEmail()
    {
        $model = new ChangeEmailForm();

        $model->setUser(\Yii::$app->user->getIdentity());

        $settingsChange = Settings::getLast(\Yii::$app->user->getId(), Settings::TYPE_CHANGE_EMAIL);

        if (empty($settingsChange)) {
            $settingsChange = Settings::create(\Yii::$app->user->getId(), Settings::TYPE_CHANGE_EMAIL, ['email' => null], false);
        }

        $model->setSettingsChange($settingsChange);

        $justSaved = false;
        if ($model->load(Yii::$app->request->post()) && $model->doChange()) {

            if ($model->getIsNotEqualsOldEmail()) {
                return $this->redirect(
                    Url::to(['/cabinet/settings/confirm-email'])
                );
            }

            $justSaved = true;
        }

        return $this->render('changeEmail', [
            'model'     => $model,
            'justSaved' => $justSaved,
        ]);
    }


    public function actionCancelChangingEmail()
    {
        if (Settings::rollbackSettingsChanges(\Yii::$app->getUser()->getId(), Settings::TYPE_CHANGE_EMAIL) === false) {
            throw new Exception(\Yii::t('app', 'Change email settings is temporarily unavailable'));
        }

        return $this->redirect(
            Url::to(['/cabinet/settings/index'])
        );
    }


    public function actionChangePassword()
    {
        $model = new ChangePasswordForm();

        $model->setUser(\Yii::$app->user->getIdentity());

        $saved = false;
        if ($model->load(Yii::$app->request->post()) && $model->doChange()) {
            $saved = true;
        }

        return $this->render('changePassword', [
            'model' => $model,
            'saved' => $saved,
        ]);

    }

}
