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

use Zend\ServiceManager\Proxy\ServiceProxyAbstractFactoryFactory;
use Zend\ServiceManager\ServiceManager;

use PHPUnit_Framework_TestCase;

/**
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ServiceProxyAbstractFactoryFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $factory = new ServiceProxyAbstractFactoryFactory();
        $sm = new ServiceManager();
        $sm->setService('Config', array(
            'service_proxies_dir' => __DIR__,
            'service_proxies_ns'  => 'TestNs',
        ));
        $proxyFactory = $factory->createService($sm);


        $this->assertInstanceOf('Zend\ServiceManager\Proxy\ServiceProxyAbstractFactory', $proxyFactory);
        // verifying generator
        $proxyClass = $proxyFactory->getProxyGenerator()->getProxyClassName('TestClass');
        $this->assertSame('TestNs\__CG__\TestClass', $proxyClass);
        $proxyFile = $proxyFactory->getProxyGenerator()->getProxyFileName('TestClass');
        $this->assertSame(__DIR__ . '/__CG__TestClass.php', $proxyFile);

        // cleaning up
        $autoloaders = spl_autoload_functions();
        spl_autoload_unregister(end($autoloaders));
    }
}
