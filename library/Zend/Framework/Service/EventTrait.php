<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use Zend\Framework\Application\ServiceTrait as Services;
use Zend\Framework\EventManager\EventTrait as EventService;
use Zend\Framework\EventManager\ListenerInterface;

trait EventTrait
{
    /**
     *
     */
    use EventService, Services;

    /**
     * @var string
     */
    protected $service;

    /**
     * @var bool|object
     */
    protected $instance;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var ListenerInterface
     */
    protected $listener;

    /**
     * @return string
     */
    public function service()
    {
        return $this->service;
    }

    /**
     * @param string $service
     * @return self
     */
    public function setService($service)
    {
        $this->service = $service;
        return $this;
    }

    /**
     * @return array
     */
    public function options()
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return self
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return bool|object
     */
    public function instance()
    {
        return $this->instance;
    }

    /**
     * @param $instance
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;
    }

    /**
     * @return ListenerInterface
     */
    public function listener()
    {
        return $this->listener;
    }

    /**
     * @param ListenerInterface $listener
     */
    public function setListener(ListenerInterface $listener)
    {
        $this->listener = $listener;
    }
}
