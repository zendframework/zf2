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
use Zend\Framework\Service\ServiceInterface;

class InstanceListener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * @param ServiceManager $sm
     * @param string|callable $factory
     */
    public function __construct(ServiceManager $sm, $factory)
    {
        $this->sm      = $sm;
        $this->factory = $factory;
    }

    /**
     * @param EventInterface $event
     * @return mixed|object
     */
    public function service(EventInterface $event)
    {
        $options = $event->options();

        if ($options) {

            $class = new ReflectionClass($this->factory);
            $instance = $class->newInstanceArgs($options);

        } else {

            $instance = new $this->factory; //could be anything

        }

        if ($instance instanceof ServiceInterface) {
            $instance->__service($this->sm);
        }

        return $instance;
    }
}
