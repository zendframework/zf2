<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\View\Helper;

use Zend\Mvc\Router\RouteStackInterface;
use Zend\Stdlib\RequestInterface;
use Zend\View\Exception;
use Zend\View\Helper\AbstractHelper;

/**
 * Helper for getting the current route.
 */
class MatchedRouteName extends AbstractHelper
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
     * @throws \Zend\View\Exception\RuntimeException
     * @return string
     */
    public function __invoke()
    {
        if (null === $this->router) {
            throw new Exception\RuntimeException('No RouteStackInterface instance provided');
        }

        if (null === $this->request) {
            throw new Exception\RuntimeException('No RequestInterface instance provided');
        }

        $router = $this->router;
        $request = $this->request;

        $routeMatch = $router->match($request);

        return is_null($routeMatch) ? '' : $routeMatch->getMatchedRouteName();
    }

    /**
     * Set the router to use for assembling.
     *
     * @param RouteStackInterface $router
     * @return $this
     */
    public function setRouter(RouteStackInterface $router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * @param RequestInterface $request
     * @return $this
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }
}
