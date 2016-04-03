<?php

namespace test\base\web;

use app\base\web\Controller;
use app\base\web\Module;
use yii\base\Action;
use yii\codeception\TestCase;

class ControllerTest extends TestCase
{

    public function testSetBreadcrumbs()
    {
        $module = new Module('testModule');
        $controller = new Controller('testController', $module);
        $action = new Action('testAction', $controller);

        $controller->setBreadcrumbs($action, 'test', 'path');

        $this->assertEquals($controller->getView()->params['breadcrumbs'], [0 => ['label' => 'test', 'url' => 'path']]);
    }

}
