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
     * @var array
     */
    protected $pending = [];

    /**
     * @var ConfigInterface
     */
    protected $services;

    /**
     * @param string $alias
     * @param mixed $service
     * @return self
     */
    public function add($alias, $service)
    {
        $this->services->add($this->aliased($alias), $service);
        return $this;
    }

    /**
     * @param string $alias
     * @return object|null
     */
    public function added($alias)
    {
        return $this->services->added($this->aliased($alias));
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
     * @param string $alias
     * @param callable $callable
     * @return $this
     */
    public function assign($alias, callable $callable)
    {
        $this->services->assign($this->aliased($alias), $callable);
        return $this;
    }

    /**
     * @param string $alias
     * @return callable|null
     */
    public function assigned($alias)
    {
        return $this->services->assigned($this->aliased($alias));
    }

    /**
     * @return ApplicationConfigInterface
     */
    public function config()
    {
        return $this->config;
    }

    /**
     * @param string $alias
     * @return mixed
     */
    public function configured($alias)
    {
        return $this->services->configured($this->aliased($alias));
    }

    /**
     * @param string $alias
     * @param mixed $options
     * @return null|object
     */
    public function create($alias, $options = null)
    {
        return $this->get($alias, $options, false);
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
     * @param string $alias
     * @return bool
     */
    public function has($alias)
    {
        return $this->services->has($this->aliased($alias));
    }

    /**
     * @param string $alias
     * @return self
     */
    protected function initialized($alias)
    {
        $this->pending[$this->aliased($alias)] = false;
        return $this;
    }

    /**
     * @param string $alias
     * @return self
     */
    protected function initializing($alias)
    {
        $name = $this->aliased($alias);

        if (!empty($this->pending[$name])) {
            return true;
        }

        $this->pending[$name] = true;

        return false;
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
