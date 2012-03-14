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

use Traversable,
    Zend\Di\Locator,
    Zend\Navigation\Container,
    Zend\Navigation\Page\AbstractPage,
    Zend\Navigation\Page\Mvc as MvcPage,
    Zend\Navigation\Exception\InvalidArgumentException,
    Zend\Mvc\Router\Http\RouteMatch,
    Zend\View\Helper\Url as UrlHelper;

/**
 * A simple container class for {@link Zend_Navigation_Page} pages
 *
 *
 * @category  Zend
 * @package   Zend_Navigation
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Navigation extends Container
{
    /**
     * Service locator instance
     * @var Zend\Di\Locator
     */
    protected $locator;

    /**
     * Route match
     * @var Zend\Mvc\Router\Http\RouteMatch
     */
    protected $routeMatch;

    /**
     * Url view helper
     * @var Zend\View\Helper\Url
     */
    protected $urlHelper;


    /**
     * Construct
     * Instantiates navigation and injects dependencies to mvc pages.
     *
     * @param array|Container $pages
     * @param Zend\Mvc\Router\Http\RouteMatch $routeMatch
     * @param Zend\View\Helper\Url $urlHelper
     * @param Zend\Di\Locator $locator
     * @return void
     */
    public function __construct(
        $pages = null,
        $routeMatch = null,
        $urlHelper = null,
        Locator $locator = null)
    {
        //set locator
        $this->locator = $locator;

        //set route match
        if($routeMatch) {
            $this->setRouteMatch($routeMatch);
        }

        //set url helper
        if($urlHelper) {
            $this->setUrlHelper($urlHelper);
        }

        //set default url helper for mvc pages
        if($locator) {
            MvcPage::setDefaultUrlHelper($locator->get('Zend\View\Helper\Url'));
        }

        //add pages
        if($pages) {
            $this->addPages($pages);
        }
    }


    /**
     * Add page
     * Adds a page to navigation container. In case MVC page is discovered
     * injects dependencies.
     *
     * @param Zend\Navigation\Page\AbstractPage|array $page
     * @return Zend\Navigation\Navigation
     */
    public function addPage($page)
    {
        $routeMatch = $this->getRouteMatch();
        $urlHelper = $this->getUrlHelper();

        //inject dependencies to mvc page
        if($this->isMvcPage($page) && $page instanceof MvcPage) {
            $pageHelper = $page->getUrlHelper();
            if(!$pageHelper && $urlHelper) {
                $page->setUrlHelper($urlHelper);
            }

            $pageMatch  = $page->getRouteMatch();
            if(!$pageMatch && $routeMatch) {
                $page->setRouteMatch($routeMatch);
            }
        }

        //add dependencies to mvc page options
        if($this->isMvcPage($page) && is_array($page))
        {
            if(!isset($page['urlHelper']) && $urlHelper) {
                $page['urlHelper'] = $urlHelper;
            }
            if(!isset($page['routeMatch']) && $routeMatch) {
                $page['routeMatch'] = $routeMatch;
            }
        }

        parent::addPage($page);
        return $this;
    }


    /**
     * Set url helper
     * Sets injected url helper instance
     *
     * @param Zend\View\Helper\Url $helper
     * @return Zend\Navigation\Navigation
     */
    public function setUrlHelper(UrlHelper $helper)
    {
        $this->urlHelper = $helper;
        return $this;
    }


    /**
     * Get url helper
     * Returns currently injected url helper.
     * @return Zend\View\Helper\Url|null
     */
    public function getUrlHelper()
    {
        return $this->urlHelper;
    }


    /**
     * Set route match
     * Sets injected route match instance
     *
     * @param Zend\Mvc\Router\Http\RouteMatch $routeMatch
     * @return Zend\Navigation\Navigation
     */
    public function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
        return $this;
    }


    /**
     * Get route match
     * Returns current route match instance if present
     * @return Zend\Mvc\Router\Http\RouteMatch|null
     */
    public function getRouteMatch()
    {
        return $this->routeMatch;
    }


    /**
     * Is MVC page
     * Checks if a page is of MVC type
     * @param array|Zend\Navigation\Page\AbstractPage
     * @return bool
     */
    protected function isMvcPage($page)
    {
        //check page type
        if(!is_array($page) && !$page instanceof AbstractPage) {
            return false;
        }

        //of mvc type already?
        if($page instanceof MvcPage) {
            return true;
        }


        //detect type for array
        if(is_array($page)) {
            $hasModule = isset($page['module']);
            $hasController = isset($page['controller']);
            $hasAction = isset($page['action']);
            $hasRoute = isset($page['route']);

            if($hasModule || $hasController || $hasAction || $hasRoute) {
                return true;
            }
        }

        //otherwise false
        return false;
    }



}
