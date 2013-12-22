<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\ServiceManager;

use ReflectionClass;

use Zend\Framework\ServiceManager\ServiceRequestInterface as ServiceRequest;
use Zend\Framework\ServiceManager\ServiceListenerInterface as ServiceListener;

class ServiceInvokableFactoryListener
    implements ServiceListener
{
    /**
     *
     */
    const FACTORY_INTERFACE = 'Zend\Framework\ServiceManager\FactoryInterface';

    /**
     * @param ServiceRequest $service
     * @return bool|mixed|object
     */
    public function __invoke(ServiceRequest $service)
    {
        $sm = $service->getTarget();

        $name = $service->getName();

        $factory = $sm->getConfig($name);

        if (!$factory) {
            return false;
        }

        $options = $service->getOptions();

        if (is_string($factory)) {
            $class = new ReflectionClass($factory);

            $factory = $class->newInstanceArgs($options);

            if ($class->implementsInterface(self::FACTORY_INTERFACE)) {
                    return $factory->createService($sm);
            }

            return $factory;
        }

        if (is_callable($factory)) {
            return call_user_func_array($factory, [$sm, $options]);
        }

        return $factory->createService($sm, $options);
    }
}
