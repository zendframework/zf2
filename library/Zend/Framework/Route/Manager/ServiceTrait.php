<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Manager;

use Zend\Stdlib\RequestInterface;

trait ServiceTrait
{

    /**
     * Route manager
     *
     * @var ManagerInterface
     */
    protected $rm;

    /**
     * @param RequestInterface $request
     * @return mixed
     */
    public function route(RequestInterface $request)
    {
        return $this->rm->route($request);
    }

    /**
     * Set the route manager.
     *
     * @param  ManagerInterface $rm
     * @return self
     */
    public function setRouteManager(ManagerInterface $rm)
    {
        $this->rm = $rm;
        return $this;
    }

    /**
     * Get the route manager.
     *
     * @return ManagerInterface
     */
    public function routeManager()
    {
        return $this->rm;
    }
}
