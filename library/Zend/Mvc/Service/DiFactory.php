<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Zend\Mvc\Service;

use Zend\Di\Config as DiConfig;
use Zend\Di\Di;
use Zend\ServiceManager\Di\DiAbstractServiceFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 */
class DiFactory implements FactoryInterface
{
    /**
     * Create and return abstract factory seeded by dependency injector
     *
     * Creates and returns an abstract factory seeded by the dependency
     * injector. If the "di" key of the configuration service is set, that
     * sub-array is passed to a DiConfig object and used to configure
     * the DI instance. The DI instance is then used to seed the
     * DiAbstractServiceFactory, which is then registered with the service
     * manager.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Di
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $di     = new Di();
        $config = $serviceLocator->get('Config');

        if (isset($config['di'])) {
            $di->configure(new DiConfig($config['di']));
        }

        if ($serviceLocator instanceof ServiceManager) {
            /* @var $serviceLocator ServiceManager */
            //$serviceLocator->addAbstractFactory($serviceLocator->get('DiAbstractServiceFactory'));
            //$serviceLocator->addInitializer($serviceLocator->get('DiServiceInitializer'));
        }

        return $di;
    }
}
