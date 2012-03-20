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
    Zend\Mvc\Router\Http\RouteMatch,
    Zend\View\Helper\Url as UrlHelper;

/**
 * A simple container class for {@link Zend_Navigation_Page} pages
 *
 * @category  Zend
 * @package   Zend_Navigation
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Navigation extends Container
{
    /**
     * Construct navigation
     * Creates a new navigation container. May accept an array of page objects
     * or configuration to instantiate itself from.
     *
     * @param  array|Traversable $pages
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public function __construct(
        $pages = null,
        UrlHelper $urlHelper = null,
        RouteMatch $routeMatch = null)
    {
        //check pages
        if ($pages && !is_array($pages) && !$pages instanceof Traversable) {
            $error  = 'Invalid argument: $pages must be an array, an ';
            $error .= 'instance of Traversable, or null.';
            throw new Exception\InvalidArgumentException($error);
        }

        //set helper
        if($urlHelper){
            $this->setUrlHelper($urlHelper);
        }

        //set route match
        if($routeMatch){
            $this->setRouteMatch($routeMatch);
        }

        //add pages
        if ($pages) {
            $this->addPages($pages);
        }
    }
}
