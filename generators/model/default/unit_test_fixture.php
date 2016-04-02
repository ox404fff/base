<?php
/**
 * This is the template for generating the fixture class for a model.
 */

/* @var $this yii\web\View */
/* @var $generator app\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $modelName string model name, without namespace */


echo "<?\n";
?>

namespace test\fixtures;

use yii\test\ActiveFixture;

class <?= $modelName ?>Fixture extends ActiveFixture
{

    public $modelClass = '<?= $generator->ns ?>\<?= $modelName ?>';

}