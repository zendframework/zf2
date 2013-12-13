<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\ServiceManager;

use Zend\Framework\ServiceManager\ServiceInvokableFactoryListener;
use Zend\Framework\ServiceManager\ServiceAbstractFactoryListener;
use Zend\Framework\ServiceManager\ServiceListenerInterface as ServiceListener;

use Zend\Framework\ServiceManager\ServiceRequestInterface;

class ServiceRequest
    implements ServiceRequestInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var bool
     */
    protected $shared = true;

    /**
     * @var ServiceManager
     */
    protected $target;

    /**
     * @var array
     */
    protected $options = [];

    /**
     *
     */
    public function __construct($name, array $options = [], $shared = true)
    {
        $this->name = $name;
        $this->options = $options;
        $this->shared = $shared;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getListeners()
    {
        return [new ServiceInvokableFactoryListener, new ServiceAbstractFactoryListener];
    }

    /**
     * @return bool
     */
    public function isShared()
    {
        return $this->shared;
    }

    /**
     * @return ServiceManager
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @param ServiceListener $listener
     * @return mixed
     */
    public function  __invoke(ServiceListener $listener)
    {
        return $listener($this);
    }
}
