<?
use \yii\bootstrap as b;
use \yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\modules\auth\forms\SetPasswordForm */
/* @var $isPasswordSet bool */


$this->title = 'Setting new password';
$this->params['breadcrumbs'][] = $this->title;

?>

<? if ($isPasswordSet): ?>
    <? b\Alert::begin(['options' => ['class' => 'alert-success'], 'closeButton' => false]) ?>
    <?= \Yii::t('app', 'New password successfully set.') ?>
    <? b\Alert::end() ?>

    <?= b\Html::a('Back to home', Url::home(), ['class' => 'btn btn-primary']) ?>
<? elseif ($model->hasErrors('code')): ?>

<? b\Alert::begin(['options' => ['class' => 'alert-danger'], 'closeButton' => false]) ?>
    <h4><?= \Yii::t('app', 'Resetting password code is invalid:') ?></h4>
    <? foreach ($model->getErrors('code') as $codeErrorText): ?>
    <p><?= \Yii::t('app', $codeErrorText) ?></p>
    <? endforeach ?>
    <p>
        <a class="btn btn-danger" href="<?= Url::to(['/auth/forgot/index']) ?>"><?= \Yii::t('app', 'Send new code') ?></a>
        <a class="btn btn-default" href="<?= Url::home() ?>"><?= \Yii::t('app', 'Back to home') ?></a>
    </p>
<? b\Alert::end() ?>

<? else: ?>
<div class="forgot-password">
    <h1><?= b\Html::encode($this->title) ?></h1>

    <?php $form = b\ActiveForm::begin([
        'id' => 'forgot-form',
        'action' => ['/auth/forgot/set-password'],
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

    <?= b\Html::activeHiddenInput($model, 'code') ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= b\Html::submitButton('Change', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

    <?php b\ActiveForm::end(); ?>
</div>
<? endif ?>