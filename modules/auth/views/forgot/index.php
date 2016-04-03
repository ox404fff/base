<?
use \yii\helpers\Url;
use \yii\bootstrap as b;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\modules\auth\forms\LoginForm */
/* @var $isCodeSend bool  */


?>
<? if ($isCodeSend): ?>
    <? b\Alert::begin(['options' => ['class' => 'alert-success'], 'closeButton' => false]) ?>
    <?= \Yii::t('app', 'Instructions successfully sent in email.') ?>
    <?= \Yii::t('app', 'Address of the recipient').' <strong>"'.$model->login.'"</strong>' ?>
    <? b\Alert::end() ?>

    <?= b\Html::a('Back to home', Url::home(), ['class' => 'btn btn-primary']) ?>
<? else: ?>
<div class="forgot-password">
    <h1><?= b\Html::encode($this->title) ?></h1>

    <?php $form = b\ActiveForm::begin([
        'id' => 'forgot-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

    <?= $form->field($model, 'login') ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= b\Html::submitButton('Send recovery instructions', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

    <?php b\ActiveForm::end(); ?>

</div>
<? endif ?>