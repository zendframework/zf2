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
use ZendTest\ServiceManager\TestAsset\PublicPropertiesLazyService;

/**
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ServiceProxyAbstractFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceProxyAbstractFactory
     */
    protected $factory;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->factory = new ServiceProxyAbstractFactory(new Memory());
    }

    /**
     * @covers \Zend\ServiceManager\Proxy\ServiceProxyAbstractFactory::canCreateServiceWithName
     */
    public function testWillNotCreateProxiesFromGenericServiceLocators()
    {
        $sm = new ServiceManager();
        $this->assertTrue($this->factory->canCreateServiceWithName($sm, 'name', 'name'));

        $sl = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $this->assertFalse($this->factory->canCreateServiceWithName($sl, 'name', 'name'));
    }

    /**
     * @covers \Zend\ServiceManager\Proxy\ServiceProxyAbstractFactory::createServiceWithName
     */
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

    /**
     * @covers \Zend\ServiceManager\Proxy\ServiceProxyAbstractFactory::createServiceWithName
     */
    public function testCanCreateServiceWithNameProducesLazyLoadingService()
    {
        $lazyService = new LazyService();
        $sm = $this->getMock('Zend\ServiceManager\ServiceManager');
        $sm->expects($this->any())->method('create')->with('lazy-service')->will($this->returnValue($lazyService));

        // first code generation - required to avoid fetching an initialized proxy
        $this->factory->createServiceWithName($sm, 'lazy-service', 'lazy-service');

        $uninitializedProxy = $this->factory->createServiceWithName($sm, 'lazy-service', 'lazy-service');
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

    /**
     * @covers \Zend\ServiceManager\Proxy\ServiceProxyAbstractFactory::createServiceWithName
     */
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

    /**
     * @covers \Zend\ServiceManager\Proxy\ServiceProxyAbstractFactory::createServiceWithName
     */
    public function testProxyGenerationProducesInitializedProxyAtFirstRun()
    {
        $service = $this->getMock('stdClass');
        $sm = $this->getMock('Zend\ServiceManager\ServiceManager');
        $sm->expects($this->any())->method('create')->with('std-class-service')->will($this->returnValue($service));

        $proxy = $this->factory->createServiceWithName($sm, 'std-class-service', 'std-class-service');
        $this->assertTrue($proxy->__isInitialized());
        $this->assertSame($service, $proxy->__wrappedObject__);
    }

    /**
     * @covers \Zend\ServiceManager\Proxy\ServiceProxyAbstractFactory::setProxyGenerator
     * @covers \Zend\ServiceManager\Proxy\ServiceProxyAbstractFactory::getProxyGenerator
     */
    public function testSetGetProxyGenerator()
    {
        $generator = $this->factory->getProxyGenerator();
        $this->assertInstanceOf('Zend\ServiceManager\Proxy\ServiceProxyGenerator', $generator);

        $mockGenerator = $this->getMock('Zend\ServiceManager\Proxy\ServiceProxyGenerator');
        $this->factory->setProxyGenerator($mockGenerator);
        $this->assertSame($mockGenerator, $this->factory->getProxyGenerator());
    }

    /**
     * @covers \Zend\ServiceManager\Proxy\ServiceProxyAbstractFactory::createServiceWithName
     */
    public function testCloneInitializedService()
    {
        $lazyService = new LazyService();
        $lazyService->increment();
        $sm = $this->getMock('Zend\ServiceManager\ServiceManager');
        $sm->expects($this->any())->method('create')->with('lazy-service')->will($this->returnValue($lazyService));

        // first code generation - required to avoid fetching an initialized proxy
        $this->factory->createServiceWithName($sm, 'lazy-service', 'lazy-service');

        $proxy = $this->factory->createServiceWithName($sm, 'lazy-service', 'lazy-service');
        $proxy->__load();

        $proxy->increment();
        $this->assertSame($lazyService->count(), $proxy->count());

        $cloned = clone $proxy;
        $this->assertSame($proxy->count(), $cloned->count());
        $this->assertNotSame($proxy->__wrappedObject__, $cloned->__wrappedObject__);

        $proxy->increment();
        $this->assertSame($proxy->count() - 1, $cloned->count());

        $cloned->increment();
        $this->assertSame($proxy->count(), $cloned->count());
    }

    /**
     * @covers \Zend\ServiceManager\Proxy\ServiceProxyAbstractFactory::createServiceWithName
     */
    public function testCloneUninitializedService()
    {
        $lazyService = new LazyService();
        $lazyService->increment();
        $sm = $this->getMock('Zend\ServiceManager\ServiceManager');
        $sm->expects($this->any())->method('create')->with('lazy-service')->will($this->returnValue($lazyService));

        // first code generation - required to avoid fetching an initialized proxy
        $this->factory->createServiceWithName($sm, 'lazy-service', 'lazy-service');

        $proxy = $this->factory->createServiceWithName($sm, 'lazy-service', 'lazy-service');
        $this->assertFalse($proxy->__isInitialized());

        $cloned = clone $proxy;
        $this->assertSame($proxy->count(), $cloned->count());
        $this->assertNotSame($proxy->__wrappedObject__, $cloned->__wrappedObject__);

        $proxy->increment();
        $this->assertSame($proxy->count() - 1, $cloned->count());

        $cloned->increment();
        $this->assertSame($proxy->count(), $cloned->count());
    }

    /**
     * @covers \Zend\ServiceManager\Proxy\ServiceProxyAbstractFactory::createServiceWithName
     */
    public function testProxyMagicGetterSetterIssetter()
    {
        $lazyService = new PublicPropertiesLazyService();

        $sm = $this->getMock('Zend\ServiceManager\ServiceManager');
        $sm->expects($this->any())->method('create')->with('lazy-service')->will($this->returnValue($lazyService));

        // first code generation - required to avoid fetching an initialized proxy
        $this->factory->createServiceWithName($sm, 'lazy-service', 'lazy-service');

        $proxy = $this->factory->createServiceWithName($sm, 'lazy-service', 'lazy-service');
        $this->assertFalse($proxy->__isInitialized());

        // checking `__get`
        $this->assertSame('checkedPropertyValue', $proxy->checkedProperty);
        $lazyService->checkedProperty = 'newValue';
        $this->assertSame('newValue', $proxy->checkedProperty);

        // checking `__set`
        $proxy->checkedProperty = 'otherValue';
        $this->assertSame('otherValue', $lazyService->checkedProperty);

        // checking `__isset`
        $this->assertTrue(isset($proxy->checkedProperty));
        $lazyService->checkedProperty = null;
        $this->assertFalse(isset($proxy->checkedProperty));
    }

    /**
     * @covers \Zend\ServiceManager\Proxy\ServiceProxyAbstractFactory::createServiceWithName
     */
    public function testSerializeUninitializedProxy()
    {
        $lazyService = new PublicPropertiesLazyService();
        $lazyService->checkedProperty = 'serializedValue';

        $sm = $this->getMock('Zend\ServiceManager\ServiceManager');
        $sm->expects($this->any())->method('create')->with('lazy-service')->will($this->returnValue($lazyService));

        // first code generation - required to avoid fetching an initialized proxy
        $this->factory->createServiceWithName($sm, 'lazy-service', 'lazy-service');

        $proxy = $this->factory->createServiceWithName($sm, 'lazy-service', 'lazy-service');
        $this->assertFalse($proxy->__isInitialized());

        $unserialized = unserialize(serialize($proxy));

        $lazyService->checkedProperty = 'changedValue';
        $this->assertSame('serializedValue', $unserialized->checkedProperty);
        $this->assertNotSame($lazyService, $unserialized->__wrappedObject__);

        $proxy->checkedProperty = 'againChangedValue';
        $this->assertSame('serializedValue', $unserialized->checkedProperty);

        $unserialized->checkedProperty = 'serializedProxyChangedValue';
        $this->assertSame('againChangedValue', $lazyService->checkedProperty);
    }
}
