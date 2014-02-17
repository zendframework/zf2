<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Manager;

trait ManagerTrait
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @return mixed
     */
    public function routeClass()
    {
        return $this->config['router_class'];
    }

    /**
     * @return mixed
     */
    public function routes()
    {
        return $this->config['routes'];
    }

    /**
     * @return mixed
     */
    public function params()
    {
        return $this->config['default_params'];
    }
}
