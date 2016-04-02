<?php
/**
 * @link https://github.com/ox404fff/base/
 * @author ox404fff
 */

namespace app\generators\module;

use yii\gii\CodeFile;
use yii\helpers\ArrayHelper;

class Generator extends \yii\gii\generators\module\Generator
{

    public $baseControllerClass = 'yii\web\Controller';

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ArrayHelper::merge(parent::requiredTemplates(), [
            'base-controller.php'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = parent::generate();

        $modulePath = $this->getModulePath();

        $files[] = new CodeFile(
            $modulePath . '/components/Controller.php',
            $this->render("base-controller.php")
        );

        return $files;
    }


    /**
     * Returns the view file for the input form of the generator.
     * The default implementation will return the "form.php" file under the directory
     * that contains the generator class file.
     * @return string the view file for the input form of the generator.
     */
    public function formView()
    {
        $class = new \ReflectionClass(new parent());

        return dirname($class->getFileName()) . '/form.php';
    }


    /**
     * @return string the base controller namespace of the module.
     */
    public function getBaseControllerNamespace()
    {
        return substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\')) . '\components';
    }

}