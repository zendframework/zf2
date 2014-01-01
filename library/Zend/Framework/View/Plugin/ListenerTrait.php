<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Plugin;

use Zend\Framework\EventManager\ListenerTrait as Listener;
use Zend\Framework\Service\Event as Plugin;
use Zend\Framework\Service\EventInterface;
//use Zend\Framework\Service\ServicesTrait as Services;

trait ListenerTrait
{
    /**
     *
     */
    use Listener;

    /**
     * @param $name
     * @return string
     */
    public function alias($name)
    {
        return $this->config->get(strtolower($name)) ? : $name;
    }

    /**
     * @param string $name
     * @param string $class
     */
    public function configure($name, $class)
    {
        $this->sm->configure($this->alias($name), $class);
    }

    /**
     * @param string $name
     * @param array $options
     * @return object
     */
    public function get($name, array $options = [])
    {
        return $this->sm->__invoke(new Plugin($this->alias($name), $options));
    }

    /**
     * @param $name
     * @param $service
     * @return self
     */
    public function add($name, $service)
    {
        return $this->sm->add($this->alias($name), $service);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return $this->sm->has($this->alias($name));
    }

    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function __invoke(EventInterface $event)
    {
        return $this->sm->__invoke($event->setService($this->alias($event->name())));
    }
}
