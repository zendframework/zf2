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

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\AbstractFactoryInterface;

use Zend\Cache\Storage\StorageInterface;

use Doctrine\Common\Proxy\ProxyGenerator;
use Doctrine\Common\Proxy\Proxy;

/**
 * Abstract Service Factory responsible of generating lazy service instances that double
 * the functionality of the actually requested ones.
 *
 * @category Zend
 * @package  Zend_ServiceManager
 * @author   Marco Pivetta <ocramius@gmail.com>
 */
class ServiceProxyAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @var ProxyGenerator
     */
    private $proxyGenerator;

    /**
     * @var StorageInterface used to store the proxy definitions
     */
    private $cache;

    public function __construct(StorageInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     *
     * @return Proxy|object
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $serviceName, $requestedName)
    {
        /* @var $serviceLocator ServiceManager */

        // FQCN is cached since we don't know anything about the requested service, and want to avoid instantiation
        if (( ! $fqcn = $this->cache->getItem($requestedName)) || ! class_exists($fqcn)) {
            $service        = $serviceLocator->create($requestedName);
            $className      = get_class($service);
            $proxyGenerator = $this->getProxyGenerator();
            $fqcn           = $proxyGenerator->getProxyClassName($className);

            $proxyGenerator->generateProxyClass(new ServiceClassMetadata($className));
            require_once $proxyGenerator->getProxyFileName($className);

            $proxy = new $fqcn(null, null);
            $proxy->__wrappedObject__ = $service;
            $proxy->__setInitialized(true);
            $this->cache->setItem($requestedName, $fqcn);

            return $proxy;
        }

        return new $fqcn(
            function (Proxy $proxy) use ($serviceLocator, $serviceName, $requestedName) {
                $proxy->__setInitializer(null);
                $proxy->__setInitialized(true);
                $proxy->__wrappedObject__ = $serviceLocator->create($serviceName);
            },
            null
        );
    }

    /**
     * {@inheritDoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $serviceLocator instanceof ServiceManager;
    }

    /**
     * @param ProxyGenerator $proxyGenerator
     */
    public function setProxyGenerator(ProxyGenerator $proxyGenerator)
    {
        $this->proxyGenerator = $proxyGenerator;
    }

    /**
     * @return ProxyGenerator
     */
    public function getProxyGenerator()
    {
        if (null === $this->proxyGenerator) {
            $this->proxyGenerator = new ServiceProxyGenerator();
        }

        return $this->proxyGenerator;
    }
}
