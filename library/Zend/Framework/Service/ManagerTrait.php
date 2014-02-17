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
     * @return callable|FactoryInterface
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
        list($name, $options) = $this->options($name, $options);
        return $this->service($this->request($name, $shared), $options);
    }

    /**
     * @param $name
     * @param null $options
     * @return array
     */
    protected function options($name, $options = null)
    {
        if (is_array($name)) {
            return [array_shift($name), $name];
        }

        if (is_array($options)) {
            return [$name, $options];
        }

        return [$name, $options ? [$options] : []];
    }

    /**
     * @param $request
     * @param bool $shared
     * @return false|RequestInterface
     */
    public function request($request, $shared = true)
    {
        return $request instanceof RequestInterface ? $request : new Request($request, $shared);
    }

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return bool|object
     * @throws Exception
     */
    protected function service(RequestInterface $request, array $options = [])
    {
        $alias    = $request->alias();
        $name     = $this->alias($alias);
        $services = $this->services;
        $config   = $services->config($name);
        $assigned = $services->assigned($name);
        $service  = $services->get($name);

        if (!$config && !$assigned && !$service) {
            return false;
        }

        if ($request->shared() && $service) {
            return $service;
        }

        if ($services->initializing($name)) {
            throw new Exception('Circular dependency: '.$alias.'::'.$name);
        }

        $service = $request->call($assigned ? : $this->factory($config), $options);

        if ($request->shared()) {
            $services->add($name, $service);
        }

        $services->initialized($name);

        return $service;
    }

    /**
     * @return ConfigInterface
     */
    public function services()
    {
        return $this->services;
    }
}
