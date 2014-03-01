<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Manager;

use Zend\Framework\Route\ConfigInterface as RouterConfig;
use Zend\Framework\Service\Factory\FactoryTrait;
use Zend\Framework\Service\ManagerTrait as ServiceManager;

trait ManagerTrait
{
    /**
     *
     */
    use FactoryTrait,
        ServiceManager;

    /**
     * @var RouterConfig
     */
    protected $router;

    /**
     * @return mixed
     */
    public function defaultParams()
    {
        return $this->router->defaultParams();
    }

    /**
     * @return array
     */
    public function plugins()
    {
        return $this->router->plugins();
    }

    /**
     * @return mixed
     */
    public function routes()
    {
        return $this->router->routes();
    }

    /**
     * @return mixed
     */
    public function routeClass()
    {
        return $this->router->routeClass();
    }
}
