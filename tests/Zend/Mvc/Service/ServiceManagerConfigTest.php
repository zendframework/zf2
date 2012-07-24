<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Service;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\EventManager\EventManager;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;


class ServiceManagerConfigTest extends TestCase
{
    public function setUp()
    {
        $this->serviceManager = new ServiceManager(
            new ServiceManagerConfig()
        );

    }

    public function testEventManagerSharedEventManagerMatchesServiceManagerSharedEventManger()
    {
        $em = new EventManager;
        $emSharedEventManager = $em->getSharedManager();

        $emSharedEventManager->attach(__CLASS__, 'test', function($e) { return; });

        $smSharedEventManager = $this->serviceManager->get('SharedEventManager');
        $this->assertSame($emSharedEventManager, $smSharedEventManager);
    }
}