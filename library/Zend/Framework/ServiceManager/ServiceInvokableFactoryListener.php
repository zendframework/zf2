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

use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceRequestInterface as ServiceRequest;

use Zend\Framework\ServiceManager\ServiceListenerInterface as ServiceListener;

class ServiceInvokableFactoryListener
    implements ServiceListener
{
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

        if (is_string($factory)) {

            $options = $service->getOptions();

            if ($options) {

                $class = new ReflectionClass($factory);

                $instance = $class->newInstanceArgs($options);

                return $instance;

            } else {

                $factory = new $factory;

            }

        }

        if ($factory instanceof FactoryInterface) {
            return $factory->createService($sm);
        }

        return $factory($sm);
    }
}
