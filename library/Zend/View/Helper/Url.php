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
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\View\Helper;

use Zend\Mvc\Router\RouteStack,
    Zend\View\Exception;

/**
 * Helper for making easy links and getting urls that depend on the routes and router
 *
 * @uses       \Zend\View\Helper\AbstractHelper
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Url extends AbstractHelper
{
    /**
     * @var RouteStack
     */
    protected $router;

    /**
     * @param RouteStack $router
     * @return Url
     */
    public function setRouter(RouteStack $router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * Generates an url given the name of a route.
     *
     * @see Zend\Mvc\Router\Route::assemble()
     * @param array $params Parameters for the link
     * @param string $name Name of the route
     * @param array $options Options for the route
     * @return string Url for the link href attribute
     * @throws Exception\RuntimeException If no router provided
     */
    public function __invoke(array $params, $name, array $options = array())
    {
        if (null === $this->router) {
            throw new Exception\RuntimeException('no router instance provided');
        }

        $options['name'] = $name;

        return $this->router->assemble($params, $options);
    }
}
