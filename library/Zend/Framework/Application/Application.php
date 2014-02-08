<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

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
     * Retrieve listener from service manager
     *
     * @param $listener
     * @return mixed
     */
    protected function listener($listener)
    {
        return is_string($listener) ? $this->sm->get($listener) : $listener;
    }

    /**
     * Push listener to top of queue
     *
     * @param string $name
     * @param ListenerInterface $listener
     * @param int $priority
     * @return self
     */
    public function push($name, ListenerInterface $listener, $priority = self::PRIORITY)
    {
        if (!isset($this->listeners[$name])) {
            $this->listeners[$name] = [];
        }

        if (!isset($this->listeners[$name][$priority])) {
            $this->listeners[$name][$priority][] = $listener;
            return $this;
        }

        array_unshift($this->listeners[$name][$priority], $listener);

        return $this;
    }
}
