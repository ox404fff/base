<?php
/**
 * This is the template for generating a controller class within a module.
 */

/* @var $this yii\web\View */
/* @var $generator app\generators\module\Generator */

echo "<?\n";
?>

namespace <?= $generator->getControllerNamespace() ?>;

use <?= $generator->getBaseControllerNamespace() ?>\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
