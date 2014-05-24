<?php

namespace ZendTest\EventManager\Resolver;

use Zend\EventManager\Provider\PrototypeProvider;

/**
 * @group Zend_EventManager
 */
class PrototypeResolverTest extends \PHPUnit_Framework_TestCase
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