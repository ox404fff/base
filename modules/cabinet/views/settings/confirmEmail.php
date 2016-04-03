<?
/* @var yii\web\View $this */
/* @var yii\bootstrap\ActiveForm $form */
/* @var app\modules\cabinet\forms\ConfirmEmailForm $model */
/* @var bool $justConfirmed */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \yii\bootstrap as b;

$this->title = 'Confirmation email address';
$this->params['breadcrumbs'][] = $this->title;
?>
<? if ($model->isEmailConfirmed()): ?>

    <? b\Alert::begin(['options' => ['class' => 'alert-success'], 'closeButton' => false]) ?>
    <?= \Yii::t('app', $justConfirmed ? 'E-mail was successfully verified' : 'E-mail was successfully verified') ?>
    <? b\Alert::end() ?>

    <?= Html::a('Back to settings', Url::to(['/cabinet/settings/index']), ['class' => 'btn btn-primary']) ?>

<? else: ?>
    <? b\Alert::begin(['options' => ['class' => 'alert-info'], 'closeButton' => false]) ?>
    <p>
    <?= \Yii::t('app', 'In your email send instructions to complete the registration') ?>
    </p>
    <? if ($model->getIfExistConfirmedEmail()): ?>
    <p>
    <?= \Yii::t('app', 'If you decide not to change the address') ?>, <?= b\Html::a(\Yii::t('app', 'click here'), ['/cabinet/settings/cancel-changing-email'], [
        'data-method' => 'post'
    ]) ?>
    </p>
    <? endif ?>
    <? b\Alert::end() ?>

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

        <?= $form->field($model, 'email')->textInput(['readonly' => true]) ?>

        <?= $form->field($model, 'code')->label(null, ['class' => 'col-lg-1 control-label control-label2lines']) ?>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton(\Yii::t('app', 'Confirm'), ['class' => 'btn btn-primary', 'name' => 'do-confirm-button']) ?>
                <span class="m-l_s"><?= b\Html::a('I did not receive the message', Url::to(['/cabinet/settings/change-email'])) ?></span>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
<? endif ?>