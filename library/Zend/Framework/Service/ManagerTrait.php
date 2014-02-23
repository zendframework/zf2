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
     * @return ConfigInterface
     */
    public function config()
    {
        return $this->get('Config');
    }

    /**
     * @param array|callable|FactoryInterface|string $factory
     * @return callable|FactoryInterface
     */
    public function factory($factory)
    {
        return $this->instance($factory);
    }

    /**
     * @param mixed $alias
     * @param mixed $options
     * @param bool $shared
     * @return false|object
     */
    public function get($alias, $options = null, $shared = true)
    {
        list($alias, $options) = $this->options($alias, $options);
        return $this->service($this->request($alias, $shared), $options);
    }

    /**
     * @param array|callable|FactoryInterface|object|string $factory
     * @return callable|FactoryInterface
     */
    abstract protected function instance($factory);

    /**
     * @param $alias
     * @param null $options
     * @return array
     */
    protected function options($alias, $options = null)
    {
        if (is_array($alias)) {
            return [array_shift($alias), $alias];
        }

        if (is_array($options)) {
            return [$alias, $options];
        }

        return [$alias, $options ? [$options] : []];
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

        $config  = $services->config($name);
        $factory = $services->assigned($name);
        $service = $services->get($name);

        if (!$config && !$factory && !$service) {
            return false;
        }

        if ($request->shared() && $service) {
            return $service;
        }

        if ($services->initializing($name)) {
            throw new Exception('Circular dependency: '.$alias.'::'.$name);
        }

        $service = $request->service($factory ? : $this->factory($config), $options);

        if ($request->shared()) {
            $services->add($name, $service);
        }

        $services->initialized($name);

        return $service;
    }
}
