<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Error;

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
     * @var string
     */
    protected $controller;

    /**
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * @param RouteMatch $routeMatch
     * @param $controller
     */
    public function __construct(RouteMatch $routeMatch = null, $controller = null)
    {
        $this->routeMatch = $routeMatch;
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function controller()
    {
        return $this->controller;
    }

    /**
     * @return RouteMatch
     */
    public function routeMatch()
    {
        return $this->routeMatch;
    }

    /**
     * @param callable $listener
     * @param array $options
     * @return mixed
     */
    public function __invoke(callable $listener, array $options = [])
    {
        list($request, $response) = $options;
        return $listener($this, $request, $response);
    }
}
