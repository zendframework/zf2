<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator\Strategy;

use Zend\Stdlib\Hydrator\Strategy\JsonStrategy;
use Zend\Json\Json;

class JsonStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testExtract()
    {
        $strategy = new JsonStrategy;
        $this->assertEquals(Json::encode(array('something' => 'foo2')), $strategy->extract(array('something' => 'foo2')));
    }

    public function testHydrate()
    {
        $strategy = new JsonStrategy;
        $encodedValue = Json::encode(array('something' => 'foo2'));
        $this->assertEquals(Json::decode($encodedValue), $strategy->hydrate($encodedValue));
        $strategy->setObjectDecodeType(Json::TYPE_ARRAY);
        $this->assertEquals(array('something' => 'foo2'), $strategy->hydrate($encodedValue));
    }
}
