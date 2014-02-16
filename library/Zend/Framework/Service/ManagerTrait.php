<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use Exception;
use Zend\Framework\Service\Factory\FactoryInterface;
use Zend\Framework\Service\Factory\ServiceTrait as ServiceFactory;

trait ManagerTrait
{
    /**
     * @var ConfigInterface
     */
    protected $services;

    /**
     * @var array
     */
    protected $alias = [];

    /**
     * @param $name
     * @param $service
     * @return self
     */
    public function add($name, $service)
    {
        $this->services->add($this->alias($name), $service);
        return $this;
    }

    /**
     * @param string $alias
     * @return string
     */
    protected function alias($alias)
    {
        return isset($this->alias[$lowercase = strtolower($alias)]) ? $this->alias[$lowercase] : $alias;
    }

    /**
     * @param array|callable|FactoryInterface|object|string $factory
     * @return FactoryInterface
     */
    abstract protected function factory($factory);

    /**
     * @param mixed $name
     * @param mixed $options
     * @param bool $shared
     * @return false|object
     */
    public function get($name, $options = null, $shared = true)
    {
        if (is_array($name)) {
            $options = $name;
            $name = array_shift($options);
        }

        $options = $options ? (is_array($options) ? $options : [$options]) : [];

        return $this->service($this->request($name, $shared), $options);
    }

    /**
     * @param EventInterface $request
     * @param array $options
     * @return bool|object
     * @throws Exception
     */
    protected function service(EventInterface $request, array $options = [])
    {
        $alias    = $request->alias();
        $name     = $this->alias($alias);
        $services = $this->services;

        $config  = $services->config($name);
        $service = $services->get($name);

        if (!$config && !$service) {
            return false;
        }

        if ($request->shared() && $service) {
            return $service;
        }

        if ($services->initializing($name)) {
            throw new Exception('Circular dependency: '.$alias.'::'.$name);
        }

        $service = $request->__invoke($this->factory($config), $options);

        if ($request->shared()) {
            $services->add($name, $service);
        }

        $services->initialized($name);

        return $service;
    }

    /**
     * @param $request
     * @param bool $shared
     * @return false|object|EventInterface
     */
    public function request($request, $shared = true)
    {
        return $request instanceof EventInterface ? $request : new Event($request, $shared);
    }

    /**
     * @return ConfigInterface
     */
    public function services()
    {
        return $this->services;
    }
}
