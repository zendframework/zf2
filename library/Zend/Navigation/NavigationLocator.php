<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Navigation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Navigation;

use Zend\Mvc\Router\RouteMatch,
    Zend\Navigation\Exception\InvalidArgumentException,
    Zend\Navigation\Page\ContainerInterface,
    Zend\Navigation\Page\Mvc as MvcPage,
    Zend\View\Helper\Url as UrlHelper;

/**
 * A simple container class for {@link Zend_Navigation_Page} pages
 *
 * @category  Zend
 * @package   Zend_Navigation
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class NavigationLocator
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $containers;

    /**
     * @var Zend\Mvc\Router\RouteMatch
     */
    protected $routeMatch;

    /**
     * @var Zend\View\Helper\Url
     */
    protected $urlHelper;

    /**
     * The locator is responsible for lazy-loading navigation containers and injecting everything required
     * to render pages.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        foreach($config as $k => $v) {
            if (is_object($v) && $v instanceof ContainerInterface) {
                $config[$v->getName()] = $v;
                unset($config[$k]);
            } else if (is_numeric($k)) {
                throw new InvalidArgumentException('page containers must be indexed with a string');
            }
        }
        $this->config = $config;
    }

    /**
     * Get a container by name. Lazy-loads the container if it has not been loaded and injects
     * the route match for mvc pages.
     *
     * @param $name
     * @return mixed
     * @throws Exception\InvalidArgumentException if no container with that name was setup.
     */
    public function getContainer($name)
    {
        if (!isset($this->config[$name])) {
            throw new InvalidArgumentException(sprintf(
                'no page container with name "%s" could be located',
                $name
            ));
        }

        if (!isset($this->containers[$name])) {
            $pages = $this->config[$name];

            if (is_string($pages)) {
                $pages = new $pages;
            }

            if ($pages instanceof ContainerInterface) {
                $pages = $pages->getPages();
            } else if (!is_array($pages)) {
                throw new InvalidArgumentException('expecting an array or a ContainerInterface');
            }

            $container = new Navigation($pages);
            foreach($container->getPages() as $page) {
                if ($page instanceof MvcPage && $this->getRouteMatch()) {
                    $page->setRouteMatch($this->getRouteMatch());
                }

                if ($this->getUrlHelper()) {
                    $page->setUrlHelper($this->getUrlHelper());
                }
            }

            $this->containers[$name] = $container;
        }

        return $this->containers[$name];
    }

    /**
     * Set urlHelper
     *
     * @param null|\Zend\View\Helper\Url $urlHelper
     * @return \Zend\Navigation\NavigationLocator
     */
    public function setUrlHelper($urlHelper)
    {
        $this->urlHelper = $urlHelper;
        return $this;
    }

    /**
     * Get urlHelper
     *
     * @return \Zend\View\Helper\Url
     */
    public function getUrlHelper()
    {
        return $this->urlHelper;
    }

    /**
     * Set routeMatch
     *
     * @param null|Zend\Mvc\Router\RouteMatch $routeMatch
     * @return \Zend\Navigation\NavigationService
     */
    public function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
        return $this;
    }

    /**
     * Get routeMatch
     *
     * @return \Zend\Mvc\Router\RouteMatch
     */
    public function getRouteMatch()
    {
        return $this->routeMatch;
    }
}