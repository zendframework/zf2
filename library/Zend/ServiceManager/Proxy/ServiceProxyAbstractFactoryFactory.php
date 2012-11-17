<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace Zend\ServiceManager\Proxy;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\Proxy\ServiceProxyGenerator;

use Doctrine\Common\Proxy\Autoloader as ProxyAutoloader;

use Zend\Cache\Storage\Adapter\Memory;

/**
 * Service factory responsible of building a ServiceProxyAbstractFactory
 *
 * @category Zend
 * @package  Zend_ServiceManager
 * @author   Marco Pivetta <ocramius@gmail.com>
 *
 * @todo move to Zend\Mvc namespace?
 */
class ServiceProxyAbstractFactoryFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return ServiceProxyAbstractFactory
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config         = $serviceLocator->get('Config');
        $proxyDir       = isset($config['service_proxies_dir']) ? $config['service_proxies_dir'] : sys_get_temp_dir();
        $proxyNamespace = isset($config['service_proxies_ns'])
            ? $config['service_proxies_ns'] : ServiceProxyGenerator::DEFAULT_SERVICE_PROXY_NS;
        $autoloader     = new ProxyAutoloader();
        $cache          = isset($config['service_proxies_cache'])
            ? $serviceLocator->get($config['service_proxies_cache']) : new Memory();
        $factory        = new ServiceProxyAbstractFactory($cache);

        $factory->setProxyGenerator(new ServiceProxyGenerator($proxyDir, $proxyNamespace));
        $autoloader->register($proxyDir, $proxyNamespace);

        return $factory;
    }
}
