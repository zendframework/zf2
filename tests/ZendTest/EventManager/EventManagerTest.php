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

use Zend\EventManager\EventManager;

class EventManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testEventManagerHasNoIdentifiersByDefault()
    {
        $eventManager = new EventManager();
        $this->assertEmpty($eventManager->getIdentifiers());
    }

    public function testCanSetIdentifiersThroughConstructor()
    {
        $identifiers  = ['identifier1', 'identifier2'];
        $eventManager = new EventManager($identifiers);

        $this->assertEquals($identifiers, $eventManager->getIdentifiers());
    }

    public function testSetIdentifiersReplace()
    {
        $eventManager = new EventManager(['identifier1']);
        $eventManager->setIdentifiers(['identifier2']);

        $this->assertEquals(['identifier2'], $eventManager->getIdentifiers());
    }

    public function testAddIdentifiersAppend()
    {
        $eventManager = new EventManager(['identifier1']);
        $eventManager->addIdentifiers(['identifier2', 'identifier2']);

        $this->assertEquals(['identifier1', 'identifier2'], $eventManager->getIdentifiers());
    }

    public function testEventManagerHasNoSharedEventManagerByDefault()
    {
        $eventManager = new EventManager();
        $this->assertNull($eventManager->getSharedManager());
    }

    public function testCanAttachListener()
    {
        $eventManager = new EventManager();
        $count        = 0;

        $eventManager->attach('event', function() use (&$count) { $count++; });
        $eventManager->attach('event', function() use (&$count) { $count++; });

        $eventManager->trigger('unknownEvent');
        $this->assertEquals(0, $count);

        $eventManager->trigger('event');
        $this->assertEquals(2, $count);

        // Test clearing listeners
        $eventManager->clearListeners('unknownEvent');
        $eventManager->trigger('event');
        $this->assertEquals(4, $count);

        $eventManager->clearListeners('event');
        $eventManager->trigger('event');
        $this->assertEquals(4, $count);
    }

    public function testCanAttachAggregate()
    {
        $listenerAggregate = $this->getMock('Zend\EventManager\ListenerAggregateInterface');
        $eventManager      = new EventManager();

        $listenerAggregate->expects($this->once())
                          ->method('attach')
                          ->with($eventManager, 1);

        $eventManager->attachAggregate($listenerAggregate);
    }

    public function testDetachUnknownListener()
    {
        $eventManager = new EventManager();

        $this->assertFalse($eventManager->detach(function() {}));
        $this->assertFalse($eventManager->detach(function() {}, 'event'));
    }

    public function testCanDetachListenerWithoutEventName()
    {
        $eventManager = new EventManager();
        $count        = 0;

        $listener = $eventManager->attach('event', function() use (&$count) { $count++; });
        $this->assertTrue($eventManager->detach($listener));

        $eventManager->trigger('event');

        $this->assertEquals(0, $count);
    }

    public function testCanDetachListenerWithEventName()
    {
        $eventManager = new EventManager();
        $count        = 0;

        $listener = $eventManager->attach('event', function() use (&$count) { $count++; });
        $this->assertTrue($eventManager->detach($listener), 'event');

        $eventManager->trigger('event');

        $this->assertEquals(0, $count);
    }

    public function testCanDetachAggregate()
    {
        $listenerAggregate = $this->getMock('Zend\EventManager\ListenerAggregateInterface');
        $eventManager      = new EventManager();

        $listenerAggregate->expects($this->once())
                          ->method('detach')
                          ->with($eventManager);

        $eventManager->detachAggregate($listenerAggregate);
    }

    public function testExtractEventNames()
    {
        $eventManager = new EventManager();

        $eventManager->attach('event1', function() {});
        $eventManager->attach('event2', function() {});

        $this->assertEquals(['event1', 'event2'], $eventManager->getEventNames());
    }

    public function testCanPrepareArgs()
    {
        $eventManager = new EventManager();
        $args         = $eventManager->prepareArgs(['value']);

        $this->assertInstanceOf('ArrayObject', $args);
        $this->assertCount(1, $args);
    }
}
