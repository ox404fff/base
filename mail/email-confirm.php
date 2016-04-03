<?
/**
 * @var string $confirmCode
 */

use \yii\helpers\Html;
use \yii\helpers\Url;
?>

<p>
<?= \Yii::t('app', 'To verify your email address, ') ?>
<?= Html::a(\Yii::t('app', 'click the link'),
    Url::to(['/cabinet/settings/confirm-email', 'code' => $confirmCode], true)
) ?>
</p>
<p>
<?= \Yii::t('app', 'Or, enter this code in the form of a confirmation e-mail addresses:') ?><br>
<?= $confirmCode ?>
</p>
