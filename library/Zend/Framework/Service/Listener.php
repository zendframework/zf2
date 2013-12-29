<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use Exception;
use ReflectionClass;

class Listener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use ListenerTrait {
        ListenerTrait::__construct as listener;
    }

    /**
     * @param $event
     * @param $target
     * @param $priority
     */
    public function __construct($event = self::EVENT_SERVICE, $target = null, $priority = null)
    {
        $this->listener($event, $target, $priority);
    }

    /**
     * @param EventInterface $event
     * @return bool|object
     * @throws Exception
     */
    public function __invoke(EventInterface $event)
    {
        $sm = $event->getServiceManager();

        $name = $event->service();

        $factory = $sm->getConfig($name);

        if (!$factory) {
            return false;
        }

        $options = $event->options();

        if (is_string($factory)) {
            $class = new ReflectionClass($factory);

            $factory = $class->newInstanceArgs($options);

            if ($class->implementsInterface(EventListenerInterface::FACTORY_INTERFACE)) {
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
