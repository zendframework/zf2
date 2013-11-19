<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_EventManager
 */

namespace ZendTest\EventManager;

use Zend\EventManager\ResponseCollection;

class ResponseCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testResponseCollection()
    {
        $responseCollection = new ResponseCollection(array('value1', 'value2', 'value3'));

        $this->assertFalse($responseCollection->stopped());
        $this->assertEquals('value3', $responseCollection->last());
        $this->assertEquals('value1', $responseCollection->first());

        $responseCollection->setStopped(true);
        $this->assertTrue($responseCollection->stopped());
    }

    public function testResponseCollectionIsIterableAndCountable()
    {
        $responseCollection = new ResponseCollection(array('value1', 'value2', 'value3'));
        $i                  = 1;

        foreach ($responseCollection as $response) {
            $this->assertEquals("value$i", $response);
            ++$i;
        }

        $this->assertCount(3, $responseCollection);
    }
}
