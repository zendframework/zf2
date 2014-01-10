<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

trait ServiceTrait
{
    /**
     * @var ListenerInterface
     */
    protected $sm;

    /**
     * @param $name
     * @param $service
     * @return self
     */
    public function add($name, $service)
    {
        $this->sm->add($name, $service);
        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return $this->sm->has($name);
    }

    /**
     * @param $name
     * @return bool|object
     */
    public function service($name)
    {
        return $this->sm->get($name);
    }

    /**
     * @return ListenerInterface
     */
    public function serviceManager()
    {
        return $this->sm;
    }

    /**
     * @param ListenerInterface $sm
     * @return self
     */
    public function setServiceManager(ListenerInterface $sm)
    {
        return $this->add('ServiceManager', $this->sm = $sm);
    }
}