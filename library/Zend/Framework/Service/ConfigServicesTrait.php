<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use Zend\Framework\Application\Config\ConfigInterface as ApplicationConfigInterface;
use Zend\Framework\Config\ConfigTrait;

trait ConfigServicesTrait
{
    /**
     *
     */
    use ConfigTrait;

    /**
     * @var array
     */
    protected $alias = [];

    /**
     * @var ConfigInterface
     */
    protected $services;

    /**
     * @param string $name
     * @param mixed $service
     * @return self
     */
    public function add($name, $service)
    {
        $this->services->add($this->aliased($name), $service);
        return $this;
    }

    /**
     * @param string $name
     * @return object|null
     */
    public function added($name)
    {
        return $this->services->added($this->aliased($name));
    }

    /**
     * @param string $alias
     * @param string $name
     * @return string
     */
    protected function alias($alias, $name)
    {
        $this->alias[strtolower($alias)] = $name;
        return $this;
    }

    /**
     * @param string $alias
     * @return string
     */
    protected function aliased($alias)
    {
        return isset($this->alias[$lowercase = strtolower($alias)]) ? $this->alias[$lowercase] : $alias;
    }

    /**
     * @param string $name
     * @param callable $callable
     * @return $this
     */
    public function assign($name, callable $callable)
    {
        $this->services->assign($this->aliased($name), $callable);
        return $this;
    }

    /**
     * @param string $name
     * @return callable|null
     */
    public function assigned($name)
    {
        return $this->services->assigned($this->aliased($name));
    }

    /**
     * @return ApplicationConfigInterface
     */
    public function config()
    {
        return $this->config;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function configured($name)
    {
        return $this->services->configured($this->aliased($name));
    }

    /**
     * @param mixed $alias
     * @param mixed $options
     * @param bool $shared
     * @return null|object
     */
    public function get($alias, $options = null, $shared = true)
    {
        list($alias, $options) = $this->options($alias, $options);

        return $this->service($this->request($alias, $shared), $options);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return $this->services->has($this->aliased($name));
    }

    /**
     * @param array|string $alias
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
     * @param string|RequestInterface $request
     * @param bool $shared
     * @return RequestInterface
     */
    abstract protected function request($request, $shared = true);

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return object
     */
    abstract protected function service(RequestInterface $request, array $options = []);
}
