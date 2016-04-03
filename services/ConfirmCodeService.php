<?php

namespace app\services;


use app\models\ConfirmCode;
use app\models\User;
use app\modules\auth\components\events\ForgotEvent;
use app\modules\auth\components\events\RegistrationEvent;
use app\modules\cabinet\components\events\ChangeEmailEvent;
use yii\base\Exception;

/**
 * User settings service
 *
 * Class Settings
 * @package app\services
 */
class ConfirmCodeService
{

    /**
     * Create confirm code model
     *
     * @param integer $userId
     * @param $type (for example ConfirmCode::TYPE_CONFIRM_EMAIL)
     * @return ConfirmCode
     */
    public static function createConfirmCode($type, $userId = null)
    {
        $confirmCodeString = \Yii::$app->getSecurity()
            ->generateRandomString(ConfirmCode::getLength($type));
        $confirmCodeString = strtolower($confirmCodeString);
        $confirmCodeModel = ConfirmCode::createCode($type, $confirmCodeString, $userId);

        return $confirmCodeModel;

    }


    /**
     * Send confirm email message
     *
     * @param User $user
     * @param $confirmCode
     */
    public static function sendConfirmEmail($user, $confirmCode)
    {

        \Yii::$app->mailer->compose('email-confirm', [
            'confirmCode' => $confirmCode
        ])
            ->setTo($user->getEmail())
            ->setFrom([\Yii::$app->params['noReplyEmail'] => 'no-reply'])
            ->setSubject('Confirm email address')
            ->setTextBody('Code confirmation e-mail addresses: '.$confirmCode)
            ->send();

    }


    /**
     * Send confirm email message
     *
     * @param User $user
     * @param $resetCode
     */
    public static function sendResetPassword($user, $resetCode)
    {

        \Yii::$app->mailer->compose('password-reset', [
            'resetCode' => $resetCode
        ])
            ->setTo($user->getEmail())
            ->setFrom([\Yii::$app->params['noReplyEmail'] => 'no-reply'])
            ->setSubject('Reset password')
            ->setTextBody('Code to rest password: '.$resetCode)
            ->send();

    }


    /**
     * Send confirm code to email on registration
     *
     * @param RegistrationEvent $event
     * @throws Exception
     */
    public static function onRegistration(RegistrationEvent $event)
    {
        $confirmCodeModel = ConfirmCodeService::createConfirmCode(ConfirmCode::TYPE_CONFIRM_EMAIL, $event->user->id);
        if (!$confirmCodeModel->save()) {
            throw new Exception(\Yii::t('app', 'Registration is temporarily unavailable'));
        }

        ConfirmCodeService::sendConfirmEmail($event->user, $confirmCodeModel->confirm_code);
    }


    /**
     * Send mail, when user send reset password code
     *
     * @param ForgotEvent $event
     * @throws Exception
     */
    public static function onSendResetPasswordCode(ForgotEvent $event)
    {
        ConfirmCodeService::sendResetPassword($event->user, $event->confirmCode->confirm_code);
    }


    /**
     * Create and send confirm email code
     *
     * @param ChangeEmailEvent $event
     * @throws Exception
     */
    public static function onSendConfirmEmail(ChangeEmailEvent $event)
    {
        $confirmCodeModel = ConfirmCodeService::createConfirmCode(ConfirmCode::TYPE_CONFIRM_EMAIL, $event->user->id);
        if (!$confirmCodeModel->save()) {
            throw new Exception(\Yii::t('app', 'Change email settings is temporarily unavailable'));
        }

        ConfirmCodeService::sendConfirmEmail($event->user, $confirmCodeModel->confirm_code);
    }
} 