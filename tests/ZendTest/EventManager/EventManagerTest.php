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

use ArrayIterator;
use stdClass;
use Zend\EventManager\Event;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ResponseCollection;
use Zend\EventManager\SharedEventManager;
use Zend\Stdlib\CallbackHandler;

/**
 * @category   Zend
 * @package    Zend_EventManager
 * @subpackage UnitTests
 * @group      Zend_EventManager
 */
class EventManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    public function setUp()
    {
        if (isset($this->message)) {
            unset($this->message);
        }
        $this->eventManager = new EventManager;
    }

    public function testAttachShouldReturnCallbackHandler()
    {
        $listener = $this->eventManager->attach('test', array($this, __METHOD__));
        $this->assertTrue($listener instanceof CallbackHandler);
    }

    public function testAttachShouldAddListenerToEvent()
    {
        $listener  = $this->eventManager->attach('test', array($this, __METHOD__));
        $listeners = $this->eventManager->getListeners('test');
        $this->assertEquals(1, count($listeners));
        $this->assertContains($listener, $listeners);
    }

    public function testAttachShouldAddEventIfItDoesNotExist()
    {
        $events = $this->eventManager->getEvents();
        $this->assertTrue(empty($events), var_export($events, 1));
        $listener = $this->eventManager->attach('test', array($this, __METHOD__));
        $events = $this->eventManager->getEvents();
        $this->assertFalse(empty($events));
        $this->assertContains('test', $events);
    }

    public function testAllowsPassingArrayOfEventNamesWhenAttaching()
    {
        $callback = function ($e) {
            return $e->getName();
        };
        $this->eventManager->attach(array('foo', 'bar'), $callback);

        foreach (array('foo', 'bar') as $event) {
            $listeners = $this->eventManager->getListeners($event);
            $this->assertTrue(count($listeners) > 0);
            foreach ($listeners as $listener) {
                $this->assertSame($callback, $listener->getCallback());
            }
        }
    }

    public function testPassingArrayOfEventNamesWhenAttachingReturnsArrayOfCallbackHandlers()
    {
        $callback = function ($e) {
            return $e->getName();
        };
        $listeners = $this->eventManager->attach(array('foo', 'bar'), $callback);

        $this->assertInternalType('array', $listeners);

        foreach ($listeners as $listener) {
            $this->assertInstanceOf('Zend\Stdlib\CallbackHandler', $listener);
            $this->assertSame($callback, $listener->getCallback());
        }
    }

    public function testDetachShouldRemoveListenerFromEvent()
    {
        $listener  = $this->eventManager->attach('test', array($this, __METHOD__));
        $listeners = $this->eventManager->getListeners('test');
        $this->assertContains($listener, $listeners);
        $this->eventManager->detach($listener);
        $listeners = $this->eventManager->getListeners('test');
        $this->assertNotContains($listener, $listeners);
    }

    public function testDetachShouldReturnFalseIfEventDoesNotExist()
    {
        $listener = $this->eventManager->attach('test', array($this, __METHOD__));
        $this->eventManager->clearListeners('test');
        $this->assertFalse($this->eventManager->detach($listener));
    }

    public function testDetachShouldReturnFalseIfListenerDoesNotExist()
    {
        $listener1 = $this->eventManager->attach('test', array($this, __METHOD__));
        $this->eventManager->clearListeners('test');
        $listener2 = $this->eventManager->attach('test', array($this, 'handleTestEvent'));
        $this->assertFalse($this->eventManager->detach($listener1));
    }

    public function testRetrievingAttachedListenersShouldReturnEmptyArrayWhenEventDoesNotExist()
    {
        $listeners = $this->eventManager->getListeners('test');
        $this->assertEquals(0, count($listeners));
    }

    public function testTriggerShouldTriggerAttachedListeners()
    {
        $listener = $this->eventManager->attach('test', array($this, 'handleTestEvent'));
        $this->eventManager->trigger('test', $this, array('message' => 'test message'));
        $this->assertEquals('test message', $this->message);
    }

    public function testTriggerShouldReturnAllListenerReturnValues()
    {
        $this->eventManager->attach('string.transform', function ($e) {
            $string = $e->getParam('string', '__NOT_FOUND__');
            return trim($string);
        });
        $this->eventManager->attach('string.transform', function ($e) {
            $string = $e->getParam('string', '__NOT_FOUND__');
            return str_rot13($string);
        });
        $responses = $this->eventManager->trigger('string.transform', $this, array('string' => ' foo '));
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertEquals(2, $responses->count());
        $this->assertEquals('foo', $responses->first());
        $this->assertEquals(\str_rot13(' foo '), $responses->last());
    }

    public function testTriggerUntilShouldReturnAsSoonAsCallbackReturnsTrue()
    {
        $this->eventManager->attach('foo.bar', function ($e) {
            $string = $e->getParam('string', '');
            $search = $e->getParam('search', '?');
            return strpos($string, $search);
        });
        $this->eventManager->attach('foo.bar', function ($e) {
            $string = $e->getParam('string', '');
            $search = $e->getParam('search', '?');
            return strstr($string, $search);
        });
        $responses = $this->eventManager->triggerUntil(
            'foo.bar',
            $this,
            array('string' => 'foo', 'search' => 'f'),
            array($this, 'evaluateStringCallback')
        );
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertSame(0, $responses->last());
    }

    public function testTriggerResponseCollectionContains()
    {
        $this->eventManager->attach('string.transform', function ($e) {
            $string = $e->getParam('string', '');
            return trim($string);
        });
        $this->eventManager->attach('string.transform', function ($e) {
            $string = $e->getParam('string', '');
            return str_rot13($string);
        });
        $responses = $this->eventManager->trigger('string.transform', $this, array('string' => ' foo '));
        $this->assertTrue($responses->contains('foo'));
        $this->assertTrue($responses->contains(\str_rot13(' foo ')));
        $this->assertFalse($responses->contains(' foo '));
    }

    public function handleTestEvent($e)
    {
        $message = $e->getParam('message', '__NOT_FOUND__');
        $this->message = $message;
    }

    public function evaluateStringCallback($value)
    {
        return (!$value);
    }

    public function testTriggerUntilShouldMarkResponseCollectionStoppedWhenConditionMet()
    {
        $this->eventManager->attach('foo.bar', function () { return 'bogus'; }, 4);
        $this->eventManager->attach('foo.bar', function () { return 'nada'; }, 3);
        $this->eventManager->attach('foo.bar', function () { return 'found'; }, 2);
        $this->eventManager->attach('foo.bar', function () { return 'zero'; }, 1);
        $responses = $this->eventManager->triggerUntil('foo.bar', $this, array(), function ($result) {
            return ($result === 'found');
        });
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertTrue($responses->stopped());
        $result = $responses->last();
        $this->assertEquals('found', $result);
        $this->assertFalse($responses->contains('zero'));
    }

    public function testTriggerUntilShouldMarkResponseCollectionStoppedWhenConditionMetByLastListener()
    {
        $this->eventManager->attach('foo.bar', function () { return 'bogus'; });
        $this->eventManager->attach('foo.bar', function () { return 'nada'; });
        $this->eventManager->attach('foo.bar', function () { return 'zero'; });
        $this->eventManager->attach('foo.bar', function () { return 'found'; });
        $responses = $this->eventManager->triggerUntil('foo.bar', $this, array(), function ($result) {
            return ($result === 'found');
        });
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertTrue($responses->stopped());
        $this->assertEquals('found', $responses->last());
    }

    public function testResponseCollectionIsNotStoppedWhenNoCallbackMatchedByTriggerUntil()
    {
        $this->eventManager->attach('foo.bar', function () { return 'bogus'; }, 4);
        $this->eventManager->attach('foo.bar', function () { return 'nada'; }, 3);
        $this->eventManager->attach('foo.bar', function () { return 'found'; }, 2);
        $this->eventManager->attach('foo.bar', function () { return 'zero'; }, 1);
        $responses = $this->eventManager->triggerUntil('foo.bar', $this, array(), function ($result) {
            return ($result === 'never found');
        });
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertFalse($responses->stopped());
        $this->assertEquals('zero', $responses->last());
    }

    public function testCanAttachListenerAggregate()
    {
        $aggregate = new TestAsset\MockAggregate();
        $this->eventManager->attachAggregate($aggregate);
        $events = $this->eventManager->getEvents();
        foreach (array('foo.bar', 'foo.baz') as $event) {
            $this->assertContains($event, $events);
        }
    }

    public function testCanAttachListenerAggregateViaAttach()
    {
        $aggregate = new TestAsset\MockAggregate();
        $this->eventManager->attachAggregate($aggregate);
        $events = $this->eventManager->getEvents();
        foreach (array('foo.bar', 'foo.baz') as $event) {
            $this->assertContains($event, $events);
        }
    }

    public function testAttachAggregateReturnsAttachOfListenerAggregate()
    {
        $aggregate = new TestAsset\MockAggregate();
        $method    = $this->eventManager->attachAggregate($aggregate);
        $this->assertSame('ZendTest\EventManager\TestAsset\MockAggregate::attach', $method);
    }

    public function testCanDetachListenerAggregates()
    {
        // setup some other event listeners, to ensure appropriate items are detached
        $listenerFooBar1 = $this->eventManager->attach('foo.bar', function () {
            return true;
        });
        $listenerFooBar2 = $this->eventManager->attach('foo.bar', function () {
            return true;
        });
        $listenerFooBaz1 = $this->eventManager->attach('foo.baz', function () {
            return true;
        });
        $listenerOther   = $this->eventManager->attach('other', function () {
            return true;
        });

        $aggregate = new TestAsset\MockAggregate();
        $this->eventManager->attachAggregate($aggregate);
        $this->eventManager->detachAggregate($aggregate);
        $events = $this->eventManager->getEvents();
        foreach (array('foo.bar', 'foo.baz', 'other') as $event) {
            $this->assertContains($event, $events);
        }

        $listeners = $this->eventManager->getListeners('foo.bar');
        $this->assertEquals(2, count($listeners));
        $this->assertContains($listenerFooBar1, $listeners);
        $this->assertContains($listenerFooBar2, $listeners);

        $listeners = $this->eventManager->getListeners('foo.baz');
        $this->assertEquals(1, count($listeners));
        $this->assertContains($listenerFooBaz1, $listeners);

        $listeners = $this->eventManager->getListeners('other');
        $this->assertEquals(1, count($listeners));
        $this->assertContains($listenerOther, $listeners);
    }

    public function testCanDetachListenerAggregatesViaDetach()
    {
        // setup some other event listeners, to ensure appropriate items are detached
        $listenerFooBar1 = $this->eventManager->attach('foo.bar', function () {
            return true;
        });
        $listenerFooBar2 = $this->eventManager->attach('foo.bar', function () {
            return true;
        });
        $listenerFooBaz1 = $this->eventManager->attach('foo.baz', function () {
            return true;
        });
        $listenerOther   = $this->eventManager->attach('other', function () {
            return true;
        });

        $aggregate = new TestAsset\MockAggregate();
        $this->eventManager->attachAggregate($aggregate);
        $this->eventManager->detachAggregate($aggregate);
        $events = $this->eventManager->getEvents();
        foreach (array('foo.bar', 'foo.baz', 'other') as $event) {
            $this->assertContains($event, $events);
        }

        $listeners = $this->eventManager->getListeners('foo.bar');
        $this->assertEquals(2, count($listeners));
        $this->assertContains($listenerFooBar1, $listeners);
        $this->assertContains($listenerFooBar2, $listeners);

        $listeners = $this->eventManager->getListeners('foo.baz');
        $this->assertEquals(1, count($listeners));
        $this->assertContains($listenerFooBaz1, $listeners);

        $listeners = $this->eventManager->getListeners('other');
        $this->assertEquals(1, count($listeners));
        $this->assertContains($listenerOther, $listeners);
    }

    public function testDetachAggregateReturnsDetachOfListenerAggregate()
    {
        $aggregate = new TestAsset\MockAggregate();
        $this->eventManager->attachAggregate($aggregate);
        $method = $this->eventManager->detachAggregate($aggregate);
        $this->assertSame('ZendTest\EventManager\TestAsset\MockAggregate::detach', $method);
    }

    public function testAttachAggregateAcceptsOptionalPriorityValue()
    {
        $aggregate = new TestAsset\MockAggregate();
        $this->eventManager->attachAggregate($aggregate, 1);
        $this->assertEquals(1, $aggregate->priority);
    }

    public function testAttachAggregateAcceptsOptionalPriorityValueViaAttachCallbackArgument()
    {
        $aggregate = new TestAsset\MockAggregate();
        $this->eventManager->attachAggregate($aggregate, 1);
        $this->assertEquals(1, $aggregate->priority);
    }

    public function testCallingEventsStopPropagationMethodHaltsEventEmission()
    {
        $this->eventManager->attach('foo.bar', function ($e) { return 'bogus'; }, 4);
        $this->eventManager->attach('foo.bar', function ($e) { $e->stopPropagation(true); return 'nada'; }, 3);
        $this->eventManager->attach('foo.bar', function ($e) { return 'found'; }, 2);
        $this->eventManager->attach('foo.bar', function ($e) { return 'zero'; }, 1);
        $responses = $this->eventManager->trigger('foo.bar', $this, array());
        $this->assertTrue($responses instanceof ResponseCollection);
        $this->assertTrue($responses->stopped());
        $this->assertEquals('nada', $responses->last());
        $this->assertTrue($responses->contains('bogus'));
        $this->assertFalse($responses->contains('found'));
        $this->assertFalse($responses->contains('zero'));
    }

    public function testCanAlterParametersWithinAEvent()
    {
        $this->eventManager->attach('foo.bar', function ($e) { $e->setParam('foo', 'bar'); });
        $this->eventManager->attach('foo.bar', function ($e) { $e->setParam('bar', 'baz'); });
        $this->eventManager->attach('foo.bar', function ($e) {
            $foo = $e->getParam('foo', '__NO_FOO__');
            $bar = $e->getParam('bar', '__NO_BAR__');
            return $foo . ":" . $bar;
        });
        $responses = $this->eventManager->trigger('foo.bar', $this, array());
        $this->assertEquals('bar:baz', $responses->last());
    }

    public function testParametersArePassedToEventByReference()
    {
        $params = array( 'foo' => 'bar', 'bar' => 'baz');
        $args   = $this->eventManager->prepareArgs($params);
        $this->eventManager->attach('foo.bar', function ($e) { $e->setParam('foo', 'FOO'); });
        $this->eventManager->attach('foo.bar', function ($e) { $e->setParam('bar', 'BAR'); });
        $responses = $this->eventManager->trigger('foo.bar', $this, $args);
        $this->assertEquals('FOO', $args['foo']);
        $this->assertEquals('BAR', $args['bar']);
    }

    public function testCanPassObjectForEventParameters()
    {
        $params = (object) array( 'foo' => 'bar', 'bar' => 'baz');
        $this->eventManager->attach('foo.bar', function ($e) { $e->setParam('foo', 'FOO'); });
        $this->eventManager->attach('foo.bar', function ($e) { $e->setParam('bar', 'BAR'); });
        $responses = $this->eventManager->trigger('foo.bar', $this, $params);
        $this->assertEquals('FOO', $params->foo);
        $this->assertEquals('BAR', $params->bar);
    }

    public function testCanPassEventObjectAsSoleArgumentToTrigger()
    {
        $event = new Event();
        $event->setName(__FUNCTION__);
        $event->setTarget($this);
        $event->setParams(array('foo' => 'bar'));
        $this->eventManager->attach(__FUNCTION__, function ($e) {
            return $e;
        });
        $responses = $this->eventManager->trigger($event);
        $this->assertSame($event, $responses->last());
    }

    public function testCanPassEventNameAndEventObjectAsSoleArgumentsToTrigger()
    {
        $event = new Event();
        $event->setTarget($this);
        $event->setParams(array('foo' => 'bar'));
        $this->eventManager->attach(__FUNCTION__, function ($e) {
            return $e;
        });
        $responses = $this->eventManager->trigger(__FUNCTION__, $event);
        $this->assertSame($event, $responses->last());
        $this->assertEquals(__FUNCTION__, $event->getName());
    }

    public function testCanPassEventObjectAsArgvToTrigger()
    {
        $event = new Event();
        $event->setParams(array('foo' => 'bar'));
        $this->eventManager->attach(__FUNCTION__, function ($e) {
            return $e;
        });
        $responses = $this->eventManager->trigger(__FUNCTION__, $this, $event);
        $this->assertSame($event, $responses->last());
        $this->assertEquals(__FUNCTION__, $event->getName());
        $this->assertSame($this, $event->getTarget());
    }

    public function testCanPassEventObjectAndCallbackAsSoleArgumentsToTriggerUntil()
    {
        $event = new Event();
        $event->setName(__FUNCTION__);
        $event->setTarget($this);
        $event->setParams(array('foo' => 'bar'));
        $this->eventManager->attach(__FUNCTION__, function ($e) {
            return $e;
        });
        $responses = $this->eventManager->triggerUntil($event, function ($r) {
            return ($r instanceof EventInterface);
        });
        $this->assertTrue($responses->stopped());
        $this->assertSame($event, $responses->last());
    }

    public function testCanPassEventNameAndEventObjectAndCallbackAsSoleArgumentsToTriggerUntil()
    {
        $event = new Event();
        $event->setTarget($this);
        $event->setParams(array('foo' => 'bar'));
        $this->eventManager->attach(__FUNCTION__, function ($e) {
            return $e;
        });
        $responses = $this->eventManager->triggerUntil(__FUNCTION__, $event, function ($r) {
            return ($r instanceof EventInterface);
        });
        $this->assertTrue($responses->stopped());
        $this->assertSame($event, $responses->last());
        $this->assertEquals(__FUNCTION__, $event->getName());
    }

    public function testCanPassEventObjectAsArgvToTriggerUntil()
    {
        $event = new Event();
        $event->setParams(array('foo' => 'bar'));
        $this->eventManager->attach(__FUNCTION__, function ($e) {
            return $e;
        });
        $responses = $this->eventManager->triggerUntil(__FUNCTION__, $this, $event, function ($r) {
            return ($r instanceof EventInterface);
        });
        $this->assertTrue($responses->stopped());
        $this->assertSame($event, $responses->last());
        $this->assertEquals(__FUNCTION__, $event->getName());
        $this->assertSame($this, $event->getTarget());
    }

    public function testDuplicateIdentifiersAreNotRegistered()
    {
        $events = new EventManager(array(__CLASS__, get_class($this)));
        $identifiers = $events->getIdentifiers();
        $this->assertSame(count($identifiers), 1);
        $this->assertSame($identifiers[0], __CLASS__);
        $events->addIdentifiers(__CLASS__);
        $this->assertSame(count($identifiers), 1);
        $this->assertSame($identifiers[0], __CLASS__);
    }

    public function testIdentifierGetterSettersWorkWithStrings()
    {
        $identifier1 = 'foo';
        $identifiers = array($identifier1);
        $this->eventManager->setIdentifiers($identifiers);
        $this->assertSame($this->eventManager->getIdentifiers(), $identifiers);

        $identifier2 = 'baz';
        $identifiers = array($identifier1, $identifier2);
        $this->eventManager->setIdentifiers($identifiers);
        $this->assertSame($this->eventManager->getIdentifiers(), $identifiers);
    }

    public function testIdentifierGetterSettersWorkWithArrays()
    {
        $identifiers = array('foo', 'bar');
        $this->eventManager->setIdentifiers($identifiers);
        $this->assertSame($this->eventManager->getIdentifiers(), $identifiers);
        $identifiers[] = 'baz';
        $this->eventManager->addIdentifiers($identifiers);

        // This is done because the keys doesn't matter, just the values
        $expectedIdentifiers = $this->eventManager->getIdentifiers();
        sort($expectedIdentifiers);
        sort($identifiers);
        $this->assertSame($expectedIdentifiers, $identifiers);
    }

    public function testIdentifierGetterSettersWorkWithTraversables()
    {
        $identifiers = new ArrayIterator(array('foo', 'bar'));
        $this->eventManager->setIdentifiers($identifiers);
        $this->assertSame($this->eventManager->getIdentifiers(), (array) $identifiers);
        $identifiers = new ArrayIterator(array('foo', 'bar', 'baz'));
        $this->eventManager->addIdentifiers($identifiers);

        // This is done because the keys doesn't matter, just the values
        $expectedIdentifiers = $this->eventManager->getIdentifiers();
        sort($expectedIdentifiers);
        $identifiers = (array) $identifiers;
        sort($identifiers);
        $this->assertSame($expectedIdentifiers, $identifiers);
    }

    public function testListenersAttachedWithWildcardAreTriggeredForAllEvents()
    {
        $test     = new stdClass;
        $test->events = array();
        $callback = function ($e) use ($test) {
            $test->events[] = $e->getName();
        };

        $this->eventManager->attach('*', $callback);
        foreach (array('foo', 'bar', 'baz') as $event) {
            $this->eventManager->trigger($event);
            $this->assertContains($event, $test->events);
        }
    }

    public function testSharedEventManagerAttachReturnsCallbackHandler()
    {
        $shared = new SharedEventManager;
        $callbackHandler = $shared->attach(
            'foo',
            'bar',
            function ($e) {
                return true;
            }
        );
        $this->assertTrue($callbackHandler instanceof CallbackHandler);
    }

    public function testTriggerSetsStopPropagationFlagToFalse()
    {
        $marker = (object) array('isPropagationStopped' => true);
        $this->eventManager->attach('foo', function ($e) use ($marker) {
            $marker->isPropagationStopped = $e->isPropagationStopped();
        });

        $event = new Event();
        $event->stopPropagation(true);
        $this->eventManager->trigger('foo', $event);

        $this->assertFalse($marker->isPropagationStopped);
        $this->assertFalse($event->isPropagationStopped());
    }

    public function testTriggerUntilSetsStopPropagationFlagToFalse()
    {
        $marker = (object) array('isPropagationStopped' => true);
        $this->eventManager->attach('foo', function ($e) use ($marker) {
            $marker->isPropagationStopped = $e->isPropagationStopped();
        });

        $criteria = function ($r) {
            return false;
        };
        $event = new Event();
        $event->stopPropagation(true);
        $this->eventManager->triggerUntil('foo', $event, $criteria);

        $this->assertFalse($marker->isPropagationStopped);
        $this->assertFalse($event->isPropagationStopped());
    }
}
