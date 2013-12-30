<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service\Factory;

use ReflectionClass;
use Zend\Framework\Service\EventInterface;
use Zend\Framework\Service\ListenerInterface as ServiceManager;

class Listener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * Constructor
     *
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
    }

    /**
     * @param EventInterface $event
     * @return bool|mixed
     */
    public function __invoke(EventInterface $event)
    {
        $factory = $event->factory();
        $options = $event->options();

        if (is_string($factory)) {
            $class = new ReflectionClass($factory);

            $factory = $class->newInstanceArgs($options);

            if ($class->implementsInterface(self::FACTORY_INTERFACE)) {
                return $factory->createService($this->sm);
            }

            return $factory;
        }

        if (is_callable($factory)) {
            return call_user_func_array($factory, [$this->sm, $options]);
        }

        return $factory->createService($this->sm, $options);
    }
}
