<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
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
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * @param RouteMatch $routeMatch
     */
    public function __construct(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
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
     * @param null $options
     * @return mixed
     */
    public function __invoke(callable $listener, $options = null)
    {
        list($request, $response) = $options;
        return $listener($this, $request, $response);
    }
}
