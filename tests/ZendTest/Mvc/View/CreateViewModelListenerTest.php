<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\View;

use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\EventManager\EventManager;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\View\Http\CreateViewModelListener;

class CreateViewModelListenerTest extends TestCase
{
    public function setUp()
    {
        $this->listener   = new CreateViewModelListener();
        $this->event      = new MvcEvent();
    }

    public function testReCastsAssocArrayEventResultAsViewModel()
    {
        $array = [
            'foo' => 'bar',
        ];
        $this->event->setResult($array);
        $this->listener->createViewModelFromArray($this->event);

        $test = $this->event->getResult();
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $test);
        $this->assertEquals($array, $test->getVariables());
    }

    public function nonAssocArrayResults()
    {
        return [
            [null],
            [false],
            [true],
            [0],
            [1],
            [0.00],
            [1.00],
            ['string'],
            [['foo', 'bar']],
            [new stdClass],
        ];
    }

    /**
     * @dataProvider nonAssocArrayResults
     */
    public function testDoesNotCastNonAssocArrayEventResults($test)
    {
        $this->event->setResult($test);

        $this->listener->createViewModelFromArray($this->event);

        $result = $this->event->getResult();
        $this->assertEquals(gettype($test), gettype($result));
        $this->assertEquals($test, $result);
    }

    public function testAttachesListenersAtExpectedPriority()
    {
        $events = new EventManager();
        $events->attachAggregate($this->listener);
        $listeners = $events->getListeners(MvcEvent::EVENT_DISPATCH);

        $expectedArrayCallback = [$this->listener, 'createViewModelFromArray'];
        $expectedNullCallback  = [$this->listener, 'createViewModelFromNull'];
        $expectedPriority      = -80;
        $foundArray            = false;
        $foundNull             = false;
        foreach ($listeners as $listener) {
            $callback = $listener->getCallback();
            if ($callback === $expectedArrayCallback) {
                if ($listener->getMetadatum('priority') == $expectedPriority) {
                    $foundArray = true;
                }
            }
            if ($callback === $expectedNullCallback) {
                if ($listener->getMetadatum('priority') == $expectedPriority) {
                    $foundNull = true;
                }
            }
        }
        $this->assertTrue($foundArray, 'Listener FromArray not found');
        $this->assertTrue($foundNull,  'Listener FromNull not found');
    }

    public function testDetachesListeners()
    {
        $events = new EventManager();
        $events->attachAggregate($this->listener);
        $listeners = $events->getListeners(MvcEvent::EVENT_DISPATCH);
        $this->assertEquals(2, count($listeners));
        $events->detachAggregate($this->listener);
        $listeners = $events->getListeners(MvcEvent::EVENT_DISPATCH);
        $this->assertEquals(0, count($listeners));
    }

    public function testViewModelCreatesViewModelWithEmptyArray()
    {
        $this->event->setResult([]);
        $this->listener->createViewModelFromArray($this->event);
        $result = $this->event->getResult();
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
    }

    public function testViewModelCreatesViewModelWithNullResult()
    {
        $this->event->setResult(null);
        $this->listener->createViewModelFromNull($this->event);
        $result = $this->event->getResult();
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
    }
}
