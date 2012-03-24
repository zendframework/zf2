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
 * @package    Zend_Mvc_Router
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Mvc\Router;

use Zend\Stdlib\Parameters;

/**
 * Route match.
 *
 * @package    Zend_Mvc_Router
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class RouteMatch extends Parameters
{
    /**
     * Matched route name.
     *
     * @var string
     */
    protected $matchedRouteName;

    /**
     * Set name of matched route.
     *
     * @param  string $name
     * @return RouteMatch
     */
    public function setMatchedRouteName($name)
    {
        $this->matchedRouteName = $name;
        return $this;
    }

    /**
     * Get name of matched route.
     *
     * @return string
     */
    public function getMatchedRouteName()
    {
        return $this->matchedRouteName;
    }

    /**
     * Alias for set() - Set a parameter.
     *
     * @see \Zend\Stdlib\Parameters::set()
     * @param  string $name
     * @param  mixed  $value
     * @return RouteMatch
     */
    public function setParam($name, $value)
    {
        return parent::set($name, $value);
    }

    /**
     * Alias for get() - Get a specific parameter.
     *
     * @see \Zend\Stdlib\Parameters::set()
     * @param  string $name
     * @param  mixed $default
     * @return mixed
     */
    public function getParam($name, $default = null)
    {
        return parent::get($name, $default);
    }

    /**
     * Get all parameters.
     *
     * @deprecated
     * @see \Zend\Stdlib\Parameters::toArray()
     * @return array
     */
    public function getParams()
    {
        return parent::toArray();
    }

}