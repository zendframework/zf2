<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace ZendTest\ServiceManager;

use Zend\ServiceManager\ServiceManager;
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use ReflectionObject;

/**
 * Non-ShareByDefault service manager test
 *
 * This will test the service manager behavior when shareByDefault is set to
 * FALSE
 */
class NoShareByDefaultTest extends TestCase
{
    /**
     * Service manager test instance
     *
     * @var \Zend\ServiceManager\ServiceManager
     */
    private $serviceManager = null;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->serviceManager = $this->createServiceManager();
    }

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        $this->serviceManager = null;
    }

    /**
     * Create a service manager instance with shareByDefault set to FALSE
     *
     * @return \Zend\ServiceManager\ServiceManager
     */
    private function createServiceManager()
    {
        $instance = new ServiceManager();
        $reflection = new ReflectionObject($instance);
        $property = $reflection->getProperty('shareByDefault');
        $property->setAccessible(true);
        $property->setValue($instance, false);

        return $instance;
    }

    /**
     * Test creating non-shared services by abstract factory when shareByDefault is FALSE
     *
     * shareByDefault = false
     * Service definition share = undefined -> not shared
     *
     * @covers Zend\ServiceManager\ServiceManager::get
     */
    public function testAbstractFactoryServiceIsNotSharedWithNoShareByDefault()
    {
        $factory = $this->getMockForAbstractClass('Zend\ServiceManager\AbstractFactoryInterface');
        $factory->expects($this->any())
                ->method('canCreateServiceWithName')
                ->will($this->returnValue(true));

        $factory->expects($this->any())
                ->method('createServiceWithName')
                ->will($this->returnCallback(function() {
                    return new stdClass();
                }));

        $this->serviceManager->addAbstractFactory($factory);
        $a = $this->serviceManager->get('testservice');

        $this->assertNotSame($a, $this->serviceManager->get('testservice'), 'Retrived service must not be shared');
    }

    /**
     * Test non-shared declared service works
     *
     * Services declared as not shared explicitly should still not be shared.
     *
     * shareByDefault = false
     * Service definition share = false -> not shared
     *
     * @covers Zend\ServiceManager\ServiceManager::get
     */
    public function testServiceDeclaredNotSharedWorksWithNoShareByDefault()
    {
        $this->serviceManager->setInvokableClass('notsharedtest', 'stdClass', false);
        $a = $this->serviceManager->get('notsharedtest');

        $this->assertNotSame($a, $this->serviceManager->get('notsharedtest'), 'Registered service is not expected to be shared');
    }

    /**
     * Test shared service works
     *
     * Even though shareByDefault is set to false, services declared as shared
     * explicitly should still be shared.
     *
     * shareByDefault = false
     * Service definition share = true -> shared
     *
     * @covers Zend\ServiceManager\ServiceManager::get
     */
    public function testServiceDeclaredSharedWorksWithNoShareByDefault()
    {
        $this->serviceManager->setInvokableClass('sharedtest', 'stdClass', true);
        $a = $this->serviceManager->get('sharedtest');

        $this->assertSame($a, $this->serviceManager->get('sharedtest'), 'Registered service is expected to be shared');
    }
}
