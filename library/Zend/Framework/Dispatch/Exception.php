<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

class Exception
    extends \Exception
{
    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $controllerClass;

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @return string
     */
    public function controllerName()
    {
        return $this->controller;
    }

    /**
     * @param $controller
     * @return self
     */
    public function setControllerName($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * @return string
     */
    public function controllerClass()
    {
        return $this->controllerClass;
    }

    /**
     * @param $controllerClass
     * @return self
     */
    public function setControllerClass($controllerClass)
    {
        $this->controllerClass = $controllerClass;
        return $this;
    }

    /**
     * @return \Exception
     */
    public function exception()
    {
        return $this->exception;
    }

    /**
     * @param \Exception $exception
     * @return self
     */
    public function setException(\Exception $exception)
    {
        $this->exception = $exception;
        return $this;
    }
}
