<?php
/**
 * @link https://github.com/ox404fff/base/
 * @author ox404fff
 */

namespace app\generators\model;

use yii\gii\CodeFile;
use yii\helpers\ArrayHelper;

class Generator extends \yii\gii\generators\model\Generator
{
    /**
     * @var bool
     */
    public $generateTests = true;

    /**
     * @var bool
     */
    public $generateFixtures = true;

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ArrayHelper::merge(parent::requiredTemplates(), [
            'unit_test.php',
            'unit_test_fixture.php',
            'unit_test_fixture_data.php',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = parent::generate();

        foreach ($this->getTableNames() as $tableName) {

            $modelClassName = $this->generateClassName($tableName);

            if ($this->generateTests) {
                $files = $this->_addTestFiles($files, $modelClassName, $tableName);
            }

            if ($this->generateFixtures) {
                $files = $this->_addFixtureFiles($files, $modelClassName, $tableName);
            }
        }

        return $files;
    }




    private function _addTestFiles($files, $modelName, $tableName)
    {
        $testNs = preg_replace('/^app/', 'test', $this->ns);

        $files[] = new CodeFile(
            \Yii::getAlias('@' . str_replace('\\', '/', $testNs) . '/'. $modelName. 'Test.php'),
            $this->render('unit_test.php', [
                'tableName'       => $tableName,
                'modelName'       => $modelName,
                'testNs'          => $testNs,
                'generateFixture' => $this->generateFixtures,
            ])
        );

        return $files;
    }


    private function _addFixtureFiles($files, $modelName, $tableName)
    {
        $files[] = new CodeFile(
            \Yii::getAlias('@test/fixtures/' .$modelName. 'Fixture.php'),
            $this->render('unit_test_fixture.php', [
                'tableName'       => $tableName,
                'modelName'       => $modelName,
                'generateFixture' => $this->generateFixtures,
            ])
        );

        $files[] = new CodeFile(
            \Yii::getAlias('@test/fixtures/data/' .$tableName. '.php'),
            $this->render('unit_test_fixture_data.php', [
                'modelName'       => $modelName,
            ])
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

}