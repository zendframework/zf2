<?php

namespace ZendTest\Db\TableGateway\Feature;

use PHPUnit_Framework_TestCase;
use Zend\Db\TableGateway\Feature\EventFeature;
use Zend\EventManager\EventManager;
use Zend\Db\TableGateway\Feature\EventFeature\TableGatewayEvent;

class EventFeatureTest extends PHPUnit_Framework_TestCase
{

    public function testConstructWithEventManager()
    {
        $eventManager = new EventManager();
        $eventManager->setIdentifiers(array('foo', 'bar'));
        $feature = new EventFeature($eventManager);
        $em = $feature->getEventManager();
        $this->assertEquals($em->getIdentifiers(), array('foo', 'bar'));
    }

    public function testConstructWithEventManagerAndEvent()
    {
        $eventManager = new EventManager();
        $eventManager->setIdentifiers(array('foo', 'bar'));

        $tableGatewayMock = $this->getMockForAbstractClass('Zend\Db\TableGateway\AbstractTableGateway');
        $event = new TableGatewayEvent();
        $event->setParam('foo', 'bar');

        $feature = new EventFeature($eventManager, $event);
        $feature->setTableGateway($tableGatewayMock);

        // test event with eveant feature
        $eventManager->attach(get_class($tableGatewayMock) . '.preInitialize', function ($e) {
            $e->setParam('foo', 'baz');
        });
        $feature->preInitialize();
        $this->assertEquals($event->getParam('foo'), 'baz');

        // test events with new event manager
        $newEventManager = new EventManager();
        $newEventManager->attach(get_class($tableGatewayMock) . '.preInitialize', function ($e) {
            $e->setParam('foo', 'bar');
        });
        $feature->setEventManager($newEventManager);
        $feature->preInitialize();
        $this->assertEquals($event->getParam('foo'), 'bar');
    }
}
