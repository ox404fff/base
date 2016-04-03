<?
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $saved bool */
/* @var $model \app\modules\cabinet\forms\ChangePasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use \yii\bootstrap as b;

$this->title = 'Changing password';
$this->params['breadcrumbs'][] = $this->title;
?>

<? if ($saved): ?>
    <? b\Alert::begin(['options' => ['class' => 'alert-success'], 'closeButton' => false]) ?>
    <?= \Yii::t('app', 'Password successfully changed') ?>
    <? b\Alert::end() ?>
<? endif ?>
<div class="settings-change-password">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'change-password-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

    <?= $form->field($model, 'password')
        ->label(null, ['class' => 'col-lg-1 control-label control-label2lines'])
        ->passwordInput() ?>

    <?= $form->field($model, 'newPassword')
        ->label(null, ['class' => 'col-lg-1 control-label control-label2lines'])
        ->passwordInput() ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton(\Yii::t('app', 'Save'), ['class' => 'btn btn-primary', 'name' => 'change-password-button']) ?>
            <?= Html::a(
                \Yii::t('app', 'Cancel'),
                Url::to(['/cabinet/settings/index']),
                ['class' => 'btn btn-default', 'name' => 'registration-button']
            ) ?>
            <span class="m-l_s"><?=
                Html::a(
                    'I don\'t remember password',
                    ['/auth/forgot/index']
                ) ?></span>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>