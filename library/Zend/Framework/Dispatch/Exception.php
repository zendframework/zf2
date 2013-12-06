<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

class Exception extends \Exception
{

    protected $controller;

    protected $controllerClass;

    protected $exception;

    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    public function setControllerClass($controllerClass)
    {
        $this->controllerClass = $controllerClass;
        return $this;
    }

    public function setException($exception)
    {
        $this->exception = $exception;
        return $this;
    }
}
