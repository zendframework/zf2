<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace ZendTest\ServiceManager\Proxy;

use Zend\ServiceManager\Proxy\ServiceProxyAbstractFactory;
use Zend\ServiceManager\ServiceManager;
use Zend\Cache\Storage\Adapter\Memory;

use PHPUnit_Framework_TestCase;

use ZendTest\ServiceManager\TestAsset\LazyService;

/**
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ServiceProxyAbstractFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceProxyAbstractFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new ServiceProxyAbstractFactory(new Memory());
    }

    public function testWillNotCreateProxiesFromGenericServiceLocators()
    {
        $sm = new ServiceManager();
        $this->assertTrue($this->factory->canCreateServiceWithName($sm, 'name', 'name'));

        $sl = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->assertFalse($this->factory->canCreateServiceWithName($sl, 'name', 'name'));
    }

    public function testCreateServiceWithNameFetchesServiceOnlyWhenProxyDefinitionIsUnknown()
    {
        $service = new \stdClass();
        $sm = $this->getMock('Zend\ServiceManager\ServiceManager');
        $sm->expects($this->once())->method('create')->with('std-class-service')->will($this->returnValue($service));

        // first code generation
        $proxy = $this->factory->createServiceWithName($sm, 'std-class-service', 'std-class-service');
        $this->assertInstanceOf('Doctrine\Common\Proxy\Proxy', $proxy);
        $this->assertInstanceOf('stdClass', $proxy);
        $proxy->__load(); // we know that it will be already loaded, but we won't make this assumption here
        $this->assertSame($service, $proxy->__wrappedObject__);

        // cached - doesn't trigger proxy generation anymore
        $uninitializedProxy = $this->factory->createServiceWithName($sm, 'std-class-service', 'std-class-service');
        $this->assertInstanceOf('Doctrine\Common\Proxy\Proxy', $uninitializedProxy);
        $this->assertInstanceOf('stdClass', $uninitializedProxy);
        $this->assertNotSame($proxy, $uninitializedProxy);
        $this->assertFalse($uninitializedProxy->__isInitialized());
        $this->assertNull($uninitializedProxy->__wrappedObject__);
    }

    public function testCanCreateServiceWithNameProducesLazyLoadingService()
    {
        $lazyService = new LazyService();
        $sm = $this->getMock('Zend\ServiceManager\ServiceManager');
        $sm->expects($this->any())->method('create')->with('std-class-service')->will($this->returnValue($lazyService));

        // first code generation - required to avoid fetching an initialized proxy
        $this->factory->createServiceWithName($sm, 'std-class-service', 'std-class-service');

        $uninitializedProxy = $this->factory->createServiceWithName($sm, 'std-class-service', 'std-class-service');
        $this->assertInstanceOf('Doctrine\Common\Proxy\Proxy', $uninitializedProxy);
        $this->assertInstanceOf('ZendTest\ServiceManager\TestAsset\LazyService', $uninitializedProxy);
        $this->assertFalse($uninitializedProxy->__isInitialized());
        $this->assertNull($uninitializedProxy->__wrappedObject__);

        $uninitializedProxy->increment();
        $this->assertTrue($uninitializedProxy->__isInitialized(), 'Proxy was initialized at first method call');
        $lazyService->increment();
        $this->assertSame(2, $uninitializedProxy->count(), 'Proxy correctly calls wrapped object');
        $this->assertSame(2, $lazyService->count());
    }

    public function testProxyInitializationReferencesOriginalService()
    {
        $service = new \stdClass();
        $sm = $this->getMock('Zend\ServiceManager\ServiceManager');
        $sm->expects($this->any())->method('create')->with('std-class-service')->will($this->returnValue($service));

        // first code generation - required to avoid fetching an initialized proxy
        $this->factory->createServiceWithName($sm, 'std-class-service', 'std-class-service');

        $proxy = $this->factory->createServiceWithName($sm, 'std-class-service', 'std-class-service');
        $proxy->__load();
        $this->assertSame($proxy->__wrappedObject__, $service);
    }

    public function testProxyGenerationProducesInitializedProxyAtFirstRun()
    {
        $service = $this->getMock('stdClass');
        $sm = $this->getMock('Zend\ServiceManager\ServiceManager');
        $sm->expects($this->any())->method('create')->with('std-class-service')->will($this->returnValue($service));

        $proxy = $this->factory->createServiceWithName($sm, 'std-class-service', 'std-class-service');
        $this->assertTrue($proxy->__isInitialized());
        $this->assertSame($service, $proxy->__wrappedObject__);
    }
}
