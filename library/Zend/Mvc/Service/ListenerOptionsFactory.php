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

use Zend\ModuleManager\Listener\ListenerOptions;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 */
class ListenerOptionsFactory implements FactoryInterface
{
    /**
     * Create the listener options service
     *
     * Creates a Zend\ModuleManager\Listener\ListenerOptions service, passing
     * it the options from the appliaction configuration service.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return ListenerOptions
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('ApplicationConfig');

        return new ListenerOptions($config['module_listener_options']);
    }
}
