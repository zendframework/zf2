<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Bootstrap;

use Zend\Framework\ApplicationServiceTrait;
use Zend\Framework\MvcEvent;

class Event
    extends MvcEvent
{
    /**
     * @var string
     */
    protected $name = MvcEvent::EVENT_BOOTSTRAP;

    /**
     * @var string
     */
    protected $error;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $controllerClass;

    /**
     * Set the error message (indicating error in handling request)
     *
     * @param  string $message
     * @return MvcEvent
     */
    public function setError($message)
    {
        $this->error = $message;
        return $this;
    }

    /**
     * Retrieve the error message, if any
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Get the currently registered controller name
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set controller name
     *
     * @param  string $name
     * @return MvcEvent
     */
    public function setController($name)
    {
        $this->controller = $name;
        return $this;
    }

    /**
     * Get controller class
     *
     * @return string
     */
    public function getControllerClass()
    {
        return $this->controllerClass;
    }

    /**
     * Set controller class
     *
     * @param string $class
     * @return MvcEvent
     */
    public function setControllerClass($class)
    {
        $this->controllerClass = $class;
        return $this;
    }
}
