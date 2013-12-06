<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

use Zend\Framework\EventManager\Event as EventManagerEvent;

class ErrorEvent extends EventManagerEvent
{
    protected $name = 'dispatch.error';

    protected $controller;

    protected $controllerClass;

    protected $error = 'error-exception';

    protected $exception;

    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function setControllerClass($controllerClass)
    {
        $this->controllerClass = $controllerClass;
        return $this;
    }

    public function getControllerClass()
    {
        return $this->controllerClass;
    }

    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setException($exception)
    {
        $this->exception = $exception;
        return $this;
    }

    public function getException()
    {
        return $this->exception;
    }
}
