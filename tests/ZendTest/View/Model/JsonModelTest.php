<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\View\Model;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Json\Json;
use Zend\View\Model\JsonModel;

class JsonModelTest extends TestCase
{
    public function testAllowsEmptyConstructor()
    {
        $model = new JsonModel();
        $this->assertInstanceOf('Zend\View\Variables', $model->getVariables());
        $this->assertEquals(array(), $model->getOptions());
    }

    public function testCanSerializeVariablesToJson()
    {
        $array = array('foo' => 'bar');
        $model = new JsonModel($array);
        $this->assertEquals($array, $model->getVariables());
        $this->assertEquals(Json::encode($array), $model->serialize());
    }


    public function testCanSerializeWithJsonpCallback()
    {
        $array = array('foo' => 'bar');
        $model = new JsonModel($array);
        $model->setJsonpCallback('callback');
        $this->assertEquals('callback(' . Json::encode($array) . ');', $model->serialize());
    }
}
