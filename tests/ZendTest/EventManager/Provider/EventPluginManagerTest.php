<?php

namespace ZendTest\EventManager\Resolver;

use Zend\EventManager\Provider\EventPluginManager;

/**
 * @group Zend_EventManager
 */
class EventPluginManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventPluginManager
     */
    protected $manager;

    public function setUp()
    {
        $this->manager = new EventPluginManager;
    }

    public function testIsNotSharedByDefault()
    {
        $this->assertFalse($this->manager->shareByDefault());
    }

    public function testValidatePlugin()
    {
        $this->assertNull($this->manager->validatePlugin($this->getMock('Zend\EventManager\EventInterface')));

        $this->setExpectedException('Zend\EventManager\Exception\RuntimeException');
        $this->manager->validatePlugin(new \stdClass);
    }
}
 