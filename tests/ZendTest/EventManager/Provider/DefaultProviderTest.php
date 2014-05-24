<?php

namespace ZendTest\EventManager\Resolver;

use Zend\EventManager\Provider\DefaultProvider;

/**
 * @group Zend_EventManager
 */
class DefaultProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultProvider
     */
    public $provider;

    public function setUp()
    {
        $this->provider = new DefaultProvider;
    }

    public function testGetEventReturnInsranceOfZendEventByDefault()
    {
        $this->assertInstanceOf('Zend\EventManager\Event', $event = $this->provider->get('test'));
        $this->assertEquals('test', $event->getName());
        $this->assertNotSame($event, $this->provider->get('test'));
    }

    public function testSetEventClass()
    {
        $this->provider->setEventClass($class = 'ZendTest\EventManager\TestAsset\CustomEvent');
        $this->assertInstanceOf($class, $this->provider->get('test'));
    }
}