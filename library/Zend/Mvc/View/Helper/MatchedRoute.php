<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\View\Helper;

use Traversable;
use Zend\Mvc\Router\RouteStackInterface;
use Zend\Stdlib\RequestInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\HelperPluginManager;

/**
 * Helper for getting the current route.
 */
class MatchedRoute extends AbstractHelper
{
    /**
     * RouteStackInterface instance.
     *
     * @var RouteStackInterface
     */
    protected $router;

    /**
     * Request instance;
     *
     * @var RequestInterface
     */
    protected $request;

    /**
     * @return string
     */
    public function __invoke()
    {
        $router = $this->router;
        $request = $this->request;

        $routeMatch = $router->match($request);

        return is_null($routeMatch) ? '' : $routeMatch->getMatchedRouteName();
    }

    /**
     * Set the router to use for assembling.
     *
     * @param RouteStackInterface $router
     * @return MatchedRoute
     */
    public function setRouter(RouteStackInterface $router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * @param \Zend\Stdlib\RequestInterface $request
     * @return MatchedRoute
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }
}
