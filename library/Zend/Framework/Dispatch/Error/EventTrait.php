<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch\Error;

use Exception;
use Zend\Framework\Service\ServicesTrait as Services;
use Zend\Framework\EventManager\EventTrait as EventService;

trait EventTrait
{
    /**
     *
     */
    use EventService, Services;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $controllerClass;

    /**
     * @var string
     */
    protected $error = 'error-exception';

    /**
     * @var Exception
     */
    protected $exception;

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
    public function controllerName()
    {
        return $this->controller;
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
     * @return string
     */
    public function controllerClass()
    {
        return $this->controllerClass;
    }

    /**
     * @param $error
     * @return self
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @return string
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * @param Exception $exception
     * @return self
     */
    public function setException(Exception $exception)
    {
        $this->exception = $exception;
        return $this;
    }

    /**
     * @return Exception
     */
    public function exception()
    {
        return $this->exception;
    }
}
