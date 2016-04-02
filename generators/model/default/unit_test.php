<?php
/**
 * This is the template for generating the unit test class for model.
 */

/* @var $this yii\web\View */
/* @var $generator app\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $modelName string model name, without namespace */
/* @var $testNs string unit test namespace */
/* @var $generateFixture bool If need generate fixtures */

echo "<?\n";
?>

namespace <?= $testNs ?>;

<? if ($generateFixture): ?>
use test\fixtures\<?= $modelName ?>Fixture;
<? endif ?>
use yii\codeception\TestCase;

class ProfileTest extends TestCase
{

<? if ($generateFixture): ?>
    public function fixtures()
    {
        return [
            '<?= $tableName ?>' => <?= $modelName ?>Fixture::className(),
        ];
    }
<? endif ?>

}
 