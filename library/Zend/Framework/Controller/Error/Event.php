<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Error;

use Exception;
use Zend\Framework\Event\EventTrait as EventTrait;
use Zend\Mvc\Router\RouteMatch;

class Event
    implements EventInterface
{
    /**
     *
     */
    use EventTrait;

    /**
     * @var callable|string
     */
    protected $controller;

    /**
     * @var Exception
     */
    protected $exception;

    /**
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * @param callable|string $controller
     * @param RouteMatch $routeMatch
     * @param Exception $exception
     */
    public function __construct($controller, RouteMatch $routeMatch, Exception $exception)
    {
        $this->controller = $controller;
        $this->routeMatch = $routeMatch;
        $this->exception  = $exception;
    }

    /**
     * @return callable|string
     */
    public function controller()
    {
        return $this->controller;
    }

    /**
     * @return Exception
     */
    public function exception()
    {
        return $this->exception;
    }

    /**
     * @return RouteMatch
     */
    public function routeMatch()
    {
        return $this->routeMatch;
    }
}
