<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Mvc\Service;

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
     * @var array
     */
    protected $listeners = [];

    /**
     * @var bool
     */
    protected $shared = true;

    /**
     * @var array
     */
    protected $options = [];

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
     * @return bool
     */
    public function shared()
    {
        return $this->shared;
    }

    /**
     * @param ListenerInterface $listener
     * @return mixed
     */
    public function  __invoke(ListenerInterface $listener)
    {
        return $listener->__invoke($this);
    }
}
