<?
/* @var $this yii\web\View */
/* @var $menu array */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap as b;

?>

<div class="auth-registration">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="list-group">
        <? foreach ($menu as $item): ?>
            <a href="<?= Url::to($item['route']) ?>" class="list-group-item">
                <? if (!empty($item['ico'])): ?>
                    <?= b\Html::tag('span', '<span class="glyphicon '.$item['ico'] .'"></span>',
                        empty($item['tooltip']) ? ['class' => 'badge'] : [
                            'class' => 'badge',
                            'data-toggle'      => 'tooltip',
                            'data-placement'   => 'left',
                            'title'            => \Yii::t('app', $item['tooltip']),
                        ]
                    ); ?>
                <? endif ?>
                <?= \Yii::t('app', $item['label']) ?>
            </a>
        <? endforeach ?>
    </div>
</div>