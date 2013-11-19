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

use Zend\EventManager\SharedEventManager;

class SharedEventManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testCanAttachAggregate()
    {
        $listenerAggregate  = $this->getMock('Zend\EventManager\SharedListenerAggregateInterface');
        $sharedEventManager = new SharedEventManager();

        $listenerAggregate->expects($this->once())
                          ->method('attachShared')
                          ->with($sharedEventManager, 1);

        $sharedEventManager->attachAggregate($listenerAggregate);
    }

    public function testCanDetachAggregate()
    {
        $listenerAggregate  = $this->getMock('Zend\EventManager\SharedListenerAggregateInterface');
        $sharedEventManager = new SharedEventManager();

        $listenerAggregate->expects($this->once())
                          ->method('detachShared')
                          ->with($sharedEventManager);

        $sharedEventManager->detachAggregate($listenerAggregate);
    }
}
