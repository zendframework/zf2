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

trait ManagerTrait
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
     * @param $name
     * @param $callable
     * @return $this
     */
    public function assign($name, callable $callable)
    {
        $this->services->assign($name, $callable);
        return $this;
    }

    /**
     * @param $name
     * @return callable|null
     */
    public function assigned($name)
    {
        return $this->services->assigned($name);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function configuration($name)
    {
        return $this->services->configuration($name);
    }

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return object
     * @throws Exception
     */
    abstract protected function create(RequestInterface $request, array $options = []);

    /**
     * @param mixed $alias
     * @param mixed $options
     * @param bool $shared
     * @return null|object
     */
    public function get($alias, $options = null, $shared = true)
    {
        list($alias, $options) = $this->options($alias, $options);

        return $this->create($this->request($alias, $shared), $options);
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return $this->services->has($name);
    }

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
     * @return RequestInterface
     */
    protected function request($request, $shared = true)
    {
        return $request instanceof RequestInterface ? $request : new Request($request, $shared);
    }

    /**
     * @param $name
     * @return object|null
     */
    public function service($name)
    {
        return $this->services->service($name);
    }
}
