<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use Zend\Framework\Service\Factory\Listener as FactoryService;

use Exception;

class Listener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use ListenerTrait {
        ListenerTrait::__construct as listener;
        ListenerTrait::__invoke as instance;
    }

    /**
     * @param $event
     * @param $target
     * @param $priority
     */
    public function __construct($event = self::EVENT_SERVICE, $target = null, $priority = null)
    {
        $this->listener($event, $target, $priority);
        $this->sm = $this;
    }

    /**
     * @param EventInterface $event
     * @return bool|object
     * @throws Exception
     */
    public function __invoke(EventInterface $event)
    {
        $name = $event->service();

        if ($event->shared() && isset($this->shared[$name])) {
            return $this->shared[$name];
        }

        if (!empty($this->pending[$name])) {
            throw new Exception('Circular dependency: '.$name);
        }

        $this->pending[$name] = true;

        if (isset($this->listeners[$name])) {

            $instance = $this->listeners[$name]->__invoke($event);

        } else {

            $listener = new FactoryService($this);

            $instance = false;

            $name = $event->service();

            $factory = $this->config($name);

            if ($factory) {

                $event->setFactory($factory);

                $instance = $listener->__invoke($event);
            }

            $this->listeners[$name] = $listener;

            if ($event->shared()) {
                $this->shared[$name] = $instance;
            }
        }

        $this->pending[$name] = false;

        return $instance;
    }
}
