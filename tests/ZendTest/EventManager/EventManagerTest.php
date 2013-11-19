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

use Zend\EventManager\Event;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\SharedEventManager;

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

    public function testCanTriggerListenersByPriority()
    {
        $eventManager = new EventManager();

        // When using listeners with same priority, assert first one is executed first
        $chain = '';
        $eventManager->attach('event', function() use (&$chain) { $chain .= '1'; });
        $eventManager->attach('event', function() use (&$chain) { $chain .= '2'; });
        $eventManager->trigger('event');

        $this->assertEquals('12', $chain);

        // Assert priority is respected
        $chain = '';
        $eventManager->clearListeners('event');
        $eventManager->attach('event', function() use (&$chain) { $chain .= '1'; }, 50);
        $eventManager->attach('event', function() use (&$chain) { $chain .= '3'; }, -50);
        $eventManager->attach('event', function() use (&$chain) { $chain .= '2'; }, 0);
        $eventManager->trigger('event');

        $this->assertEquals('123', $chain);

        // Assert wildcard is always executed, and respect priority
        $chain = '';
        $eventManager->clearListeners('event');
        $eventManager->attach('*', function() use (&$chain) { $chain .= '2'; }, -500);
        $eventManager->attach('event', function() use (&$chain) { $chain .= '1'; }, -50);
        $eventManager->trigger('event');

        $this->assertEquals('12', $chain);
    }

    public function testCanTriggerListenersWithSharedManager()
    {
        $eventManager       = new EventManager(['identifier']);
        $sharedEventManager = new SharedEventManager();

        $eventManager->setSharedManager($sharedEventManager);

        //
    }

    /**
     * This test assert that if an event manager contains listeners with same priority for event name,
     * wildcard and shared manager, those listeners are executed in an expected order (first event, then
     * wildcard, then shared manager)
     */
    public function testAssertOrder()
    {
        $eventManager       = new EventManager(['identifier']);
        $sharedEventManager = new SharedEventManager();
        $chain              = '';

        $eventManager->setSharedManager($sharedEventManager);

        $eventManager->attach('*', function() use (&$chain) { $chain .= '2'; });
        $eventManager->attach('event', function() use (&$chain) { $chain .= '1'; });
        $sharedEventManager->attach('identifier', 'event', function() use (&$chain) { $chain .= '3'; });

        $eventManager->trigger('event');

        $this->assertEquals('123', $chain);
    }

    public function testCanStopPropagationUsingEventObject()
    {
        $event        = new Event();
        $eventManager = new EventManager();
        $chain        = '';

        $eventManager->attach('event', function(EventInterface $event) use (&$chain) {
            $chain .= '1';
            $event->stopPropagation(true);
        }, 50);
        $eventManager->attach('event', function() use (&$chain) { $chain .= '2'; });

        $response = $eventManager->trigger('event', $event);

        $this->assertTrue($event->isPropagationStopped());
        $this->assertTrue($response->stopped());
        $this->assertEquals('1', $chain);
    }

    public function testCanStopPropagationUsingCallback()
    {
        $event        = new Event();
        $eventManager = new EventManager();
        $chain        = '';

        $eventManager->attach('event', function() use (&$chain) { $chain .= '1'; });
        $eventManager->attach('event', function() use (&$chain) { $chain .= '2'; });

        $response = $eventManager->trigger('event', $event, function() { return true; });

        $this->assertTrue($response->stopped());
        $this->assertEquals('1', $chain);
    }
}
