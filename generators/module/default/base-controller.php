<?php
/**
 * This is the template for generating a controller class within a module.
 */

/* @var $this yii\web\View */
/* @var $generator app\generators\module\Generator */

echo "<?php\n";
?>

namespace <?= $generator->getBaseControllerNamespace() ?>;

use <?= $generator->baseControllerClass ?> as BaseController;
use yii\helpers\ArrayHelper;

class Controller extends BaseController
{

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
        ]);
    }


   /**
    * @inheritdoc
    */
    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }

}
