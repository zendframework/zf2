<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\EventManager\Provider;

use Zend\EventManager\Provider\PrototypeProvider;

/**
 * @group Zend_EventManager
 */
class PrototypeProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PrototypeProvider
     */
    public $resolver;

    public function setUp()
    {
        $this->resolver = new PrototypeProvider();
    }

    public function testSetEventPrototype()
    {
        $prototype = $this->getMock($class = 'ZendTest\EventManager\TestAsset\CustomEvent');
        $prototype
            ->expects($this->once())
            ->method('setParam')
            ->with('cloned', true); // See CustomEvent for clone checking

        $this->assertSame($this->resolver, $this->resolver->setEventPrototype($prototype));
        $this->assertInstanceOf($class, $this->resolver->getEventPrototype());
    }

    public function testGetReturnCloneOfPrototype()
    {
        $event = $this->resolver->get('my_event_name');

        $this->assertInstanceOf('Zend\EventManager\Event', $event);
        $this->assertNotSame($event, $this->resolver->get('new'));
    }
}