<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ServiceManager;

use PHPUnit_Framework_TestCase as TestCase;

use Zend\ServiceManager\ServiceLocatorSynchronizer;
use Zend\ServiceManager\ServiceManager;
use ZendTest\ServiceManager\TestAsset\BarSynchronizedFactory;
use ZendTest\ServiceManager\TestAsset\MultipleSynchronizedFactory;

/**
 * @group Zend_ServiceManager
 */
class ServiceLocatorSynchronizerTest extends TestCase
{
    /**
     * @covers Zend\ServiceManager\ServiceLocatorSynchronizer::synchronize
     * @covers Zend\ServiceManager\ServiceLocatorSynchronizer::toSynchronize
     */
    public function testCanSetAndRetrieveServiceToSynchronize()
    {
        $synchronizer = new ServiceLocatorSynchronizer();
        $this->assertNull($synchronizer->toSynchronize());

        $synchronizer->synchronize('foo', $obj = new \StdClass());
        $this->assertEquals($obj, $synchronizer->toSynchronize());
    }

    /**
     * @covers Zend\ServiceManager\ServiceLocatorSynchronizer::attach
     */
    public function testCanAttachFactoriesWithSynchronizedServices()
    {
        $synchronizer = new ServiceLocatorSynchronizer();
        $synchronizer->attach($barFactory = new BarSynchronizedFactory());
        $synchronizer->attach($multipleFactory = new MultipleSynchronizedFactory());

        $services = $synchronizer->getSynchronizedServices();
        $this->assertEquals(array(
            'foo' => array($barFactory, $multipleFactory),
            'bar' => array($multipleFactory),
        ), $services);
    }

    /**
     * @covers Zend\ServiceManager\ServiceLocatorSynchronizer::detach
     */
    public function testCanDetachFactoriesWithSynchronizedServices()
    {
        $synchronizer = new ServiceLocatorSynchronizer();
        $synchronizer->attach($barFactory = new BarSynchronizedFactory());
        $synchronizer->attach($multipleFactory = new MultipleSynchronizedFactory());
        $synchronizer->detach($barFactory);

        $services = $synchronizer->getSynchronizedServices();
        $this->assertEquals(array(
            'foo' => array($multipleFactory),
            'bar' => array($multipleFactory),
        ), $services);
    }

    /**
     * @covers Zend\ServiceManager\ServiceLocatorSynchronizer::notify
     */
    public function testCanNotifyService()
    {
        $synchronizer = new ServiceLocatorSynchronizer();
        $synchronizer->attach($barFactory = new BarSynchronizedFactory());

        $bar = $barFactory->createService($this->getMock('Zend\ServiceManager\ServiceLocatorInterface'));
        $this->assertEquals(array('foo'), $bar->foo);

        $synchronizer->synchronize('foo', array('baz'));
        $synchronizer->notify();

        $this->assertEquals(array('baz'), $bar->foo);
    }
}
