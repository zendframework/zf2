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

use Traversable;
use Zend\EventManager\Event;

class EventTest extends \PHPUnit_Framework_TestCase
{
    public function testCanSetTargetAndParams()
    {
        $target = new \stdClass();
        $params = array('bar', 'foo');
        $event  = new Event($target, $params);

        $this->assertSame($target, $event->getTarget());
        $this->assertEquals($params, $event->getParams());
    }

    public function paramsProvider()
    {
        return array(
            array(
                'foo' => 'bar',
                'baz' => 'cat'
            ),
            array(
                new \ArrayIterator(array(
                    'foo' => 'bar',
                    'baz' => 'cat'
                ))
            )
        );
    }

    /**
     * @dataProvider paramsProvider
     */
    public function testSetParams($params)
    {
        $event = new Event();
        $event->setParams($params);

        if ($params instanceof Traversable) {
            $params = iterator_to_array($params);
        }

        $this->assertEquals($params, $event->getParams());
    }

    public function testCanGetDefaultParam()
    {
        $event = new Event();

        $event->setParam('foo', 'bar');
        $this->assertEquals('bar', $event->getParam('foo'));

        $this->assertEquals('default', $event->getParam('baz', 'default'));
    }
}
