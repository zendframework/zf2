<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route;

use Zend\Framework\Config\ConfigTrait as ConfigTrait;

class Config
    implements ConfigInterface
{
    /**
     *
     */
    use ConfigTrait;

    /**
     * @return array
     */
    public function defaultParams()
    {
        return (array) $this->configured('default_params');
    }

    /**
     * @return array
     */
    public function plugins()
    {
        return $this->configured('route_plugins');
    }

    /**
     * @return string
     */
    public function routeClass()
    {
        return $this->configured('router_class');
    }

    /**
     * @return array
     */
    public function routes()
    {
        return (array) $this->configured('routes');
    }
}
