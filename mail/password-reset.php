<?
use \yii\helpers\Html;
use \yii\helpers\Url;

/** @var string $resetCode */

?>

<p>
<?= \Yii::t('app', 'To set new password, ') ?>
<?= Html::a(\Yii::t('app', 'click the link'),
    Url::to(['/auth/forgot/set-password', 'code' => $resetCode], true)
) ?>
</p>
