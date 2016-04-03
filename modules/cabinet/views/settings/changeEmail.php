<?
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\modules\cabinet\forms\ChangeEmailForm */
/* @var $justSaved bool */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \yii\bootstrap as b;

$this->title = 'Change email address';
$this->params['breadcrumbs'][] = $this->title;

?>
<? if ($model->getIsNotEqualsOldEmail() === false && $justSaved): ?>
    <? b\Alert::begin(['options' => ['class' => 'alert-success'], 'closeButton' => false]) ?>
    <?= \Yii::t('app', 'E-mail was successfully verified') ?>
    <? b\Alert::end() ?>

    <?= Html::a('Back to settings', Url::to(['/cabinet/settings/index']), ['class' => 'btn btn-primary']) ?>
<? else: ?>
    <? if ($model->isEmailConfirmed()): ?>
        <? b\Alert::begin(['options' => ['class' => 'alert-success'], 'closeButton' => false]) ?>
        <?= \Yii::t('app', 'E-mail was successfully verified') ?>
        <? b\Alert::end() ?>
    <? else: ?>
        <? b\Alert::begin(['options' => ['class' => 'alert-info'], 'closeButton' => false]) ?>
        <?= \Yii::t('app', 'To a specified email address is sent an email with instructions') ?>
        <? b\Alert::end() ?>
    <? endif; ?>
    <div class="settings-confirm-email">
        <h1><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin([
            'id' => 'registration-form',
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-1 control-label'],
            ],
        ]); ?>

        <?= $form->field($model, 'email') ?>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <? if ($model->isEmailConfirmed()): ?>
                    <?= Html::submitButton(\Yii::t('app', 'Send'), ['class' => 'btn btn-primary', 'name' => 'registration-button']) ?>
                    <?= Html::a(
                        \Yii::t('app', 'Cancel'),
                        Url::to(['/cabinet/settings/index']),
                        ['class' => 'btn btn-default', 'name' => 'registration-button']
                    ) ?>
                <? elseif (empty($model->email)): ?>
                    <?= Html::submitButton(\Yii::t('app', 'Send'), ['class' => 'btn btn-primary', 'name' => 'registration-button']) ?>
                <? else: ?>
                    <?= Html::submitButton(\Yii::t('app', 'Resend'), ['class' => 'btn btn-primary', 'name' => 'registration-button']) ?>
                <? endif; ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
<? endif ?>