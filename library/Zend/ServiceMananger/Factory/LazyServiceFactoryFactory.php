<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager\Factory;

use ProxyManager\Configuration as ProxyConfiguration;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\GeneratorStrategy\EvaluatingGeneratorStrategy;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\Proxy\LazyServiceFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory to create a lazy factory
 */
class LazyServiceFactoryFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ServiceLocatorInterface $serviceLocator, $className, array $options = [])
    {
        // @TODO: in ZF3 we should have a FQCN key for config

        $config = $serviceLocator->get('Config');

        if (!isset($config['lazy_services'])) {
            throw new Exception\InvalidArgumentException('Missing "lazy_services" config key');
        }

        $lazyServices = $config['lazy_services'];

        if (!isset($lazyServices['class_map'])) {
            throw new Exception\InvalidArgumentException('Missing "class_map" config key in "lazy_services"');
        }

        $factoryConfig = new ProxyConfiguration();

        if (isset($lazyServices['proxies_namespace'])) {
            $factoryConfig->setProxiesNamespace($lazyServices['proxies_namespace']);
        }

        if (isset($lazyServices['proxies_target_dir'])) {
            $factoryConfig->setProxiesTargetDir($lazyServices['proxies_target_dir']);
        }

        if (!isset($lazyServices['write_proxy_files']) || !$lazyServices['write_proxy_files']) {
            $factoryConfig->setGeneratorStrategy(new EvaluatingGeneratorStrategy());
        }

        spl_autoload_register($factoryConfig->getProxyAutoloader());

        return new LazyServiceFactory(new LazyLoadingValueHolderFactory($factoryConfig), $lazyServices['class_map']);
    }
}