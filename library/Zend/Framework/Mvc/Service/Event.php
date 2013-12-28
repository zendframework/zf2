<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Mvc\Service;

use Zend\Framework\EventManager\ListenerInterface;

class Event
    implements EventInterface, EventListenerInterface
{
    /**
     *
     */
    use EventTrait {
        EventTrait::__construct as event;
    }

    /**
     * @param $service
     * @param array $options
     * @param bool $shared
     * @param $name
     */
    public function __construct($service, array $options = [], $shared = true, $name = self::EVENT_SERVICE)
    {
        $this->service = $service;
        $this->options = $options;
        $this->shared  = $shared;
        $this->event($name);
    }

    /**
     * Triggers event
     *
     * @param ListenerInterface $listener
     * @return bool Stopped
     */
    /*public function __invoke(ListenerInterface $listener)
    {
        $response = $listener->__invoke($this);

        if ($response) {
            $this->stopped = true;
        }

        return $this->stopped;
    }*/
}
