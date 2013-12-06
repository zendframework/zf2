<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ServiceManager;

use Zend\ServiceManager\ServiceListener;
use Zend\ServiceManager\FactoryInterface;
use ReflectionClass;

class ServiceInvokableFactoryListener implements ServiceListenerInterface
{
    public function __invoke($service)
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

                $factory = new $factory();

            }
        }

        if ($factory instanceof FactoryInterface) {
            return $factory->createService($sm);
        }

        return $factory($sm);
    }
}
