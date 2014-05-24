<?php

namespace ZendTest\EventManager\Resolver;

use Zend\EventManager\Resolver\DefaultResolver;

/**
 * @group Zend_EventManager
 */
class DefaultResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultResolver
     */
    public $resolver;

    public function setUp()
    {
        $this->resolver = new DefaultResolver;
    }

    public function testGetEventReturnInsranceOfZendEventByDefault()
    {
        $this->assertInstanceOf('Zend\EventManager\Event', $event = $this->resolver->get('test'));
        $this->assertEquals('test', $event->getName());
        $this->assertNotSame($event, $this->resolver->get('test'));
    }

    public function testSetEventClass()
    {
        $this->resolver->setEventClass($class = 'ZendTest\EventManager\TestAsset\CustomEvent');
        $this->assertInstanceOf($class, $this->resolver->get('test'));
    }
}