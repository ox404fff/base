<?php
/**
 * Created by PhpStorm.
 * User: ox404fff
 * Date: 19.03.16
 * Time: 13:21
 */

namespace app\generators\module;

use yii\gii\CodeFile;
use \yii\gii\generators\module\Generator as BaseGenerator;
use yii\helpers\StringHelper;

class Generator extends BaseGenerator
{

    public $baseControllerClass = 'yii\web\Controller';

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['module.php', 'base-controller.php', 'default-controller.php', 'view.php'];
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
        $modulePath = $this->getModulePath();
        $files[] = new CodeFile(
            $modulePath . '/' . StringHelper::basename($this->moduleClass) . '.php',
            $this->render("module.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/components/Controller.php',
            $this->render("base-controller.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/controllers/DefaultController.php',
            $this->render("default-controller.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/views/default/index.php',
            $this->render("view.php")
        );

        return $files;
    }



    /**
     * @return string the base controller namespace of the module.
     */
    public function getBaseControllerNamespace()
    {
        return substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\')) . '\components';
    }

}