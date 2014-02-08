<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\Event\EventInterface;
use Zend\Framework\Event\ListenerInterface;
use Zend\Framework\Event\Manager\ManagerTrait as EventManager;
use Zend\Framework\Service\ManagerInterface as ServiceManagerInterface;

class Application
    implements ApplicationInterface
{
    /**
     *
     */
    use EventManager;

    /**
     * @param ServiceManagerInterface $sm
     */
    public function __construct(ServiceManagerInterface $sm)
    {
        $this->sm = $sm;
    }

    /**
     * @param $event
     * @return EventInterface
     */
    public function event($event)
    {
        return $this->get($event);
    }

    /**
     * @param $service
     * @return false|object
     */
    public function get($service)
    {
        return is_string($service) ? $this->sm->get($service) : $service;
    }

    /**
     * Retrieve listener from service manager
     *
     * @param $listener
     * @return ListenerInterface
     */
    protected function listener($listener)
    {
        return $this->get($listener);
    }

    /**
     * @return mixed
     */
    public function run()
    {
        return $this->trigger('Application\Event');
    }

    /**
     * @param $event
     * @param null $options
     * @return mixed
     */
    public function trigger($event, $options = null)
    {
        return $this->__invoke($this->event($event), $options);
    }
}
