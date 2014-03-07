<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Stack;

use Zend\Framework\Event\EventInterface;
use Zend\Framework\Route\RouteInterface;
use Zend\Framework\Route\PriorityList;
use Zend\Stdlib\RequestInterface as Request;

/**
 * Simple route stack implementation.
 */
class Listener
    implements ListenerInterface, RouteInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     *
     */
    public function __construct(PriorityList $routes = null)
    {
        if (!$routes) {
            $routes = new PriorityList;
        }
        $this->routes = $routes;
    }

    /**
     * @param EventInterface $event
     * @param Request $request
     * @return mixed
     */
    public function __invoke(EventInterface $event, Request $request)
    {
        return $this->match($request);
    }
}
