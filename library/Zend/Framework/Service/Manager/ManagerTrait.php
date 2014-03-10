<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service\Manager;

use RuntimeException;
use Zend\Framework\Service\Container\ServiceTrait as Container;
use Zend\Framework\Service\Factory\FactoryInterface;
use Zend\Framework\Service\Request;
use Zend\Framework\Service\RequestInterface;

trait ManagerTrait
{
    /**
     *
     */
    use Container;

    /**
     * @var array
     */
    protected $pending = [];

    /**
     * @param string $name
     * @param mixed $options
     * @return null|object
     */
    public function create($name, $options = null)
    {
        return $this->get($name, $options, false);
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
     * @return null|object
     */
    public function get($name, $options = null, $shared = true)
    {
        list($name, $options) = $this->options($name, $options);

        return $this->manage($this->request($name, $shared), $options);
    }

    /**
     * @param string $name
     * @return self
     */
    protected function initialized($name)
    {
        $this->pending[$name] = false;
        return $this;
    }

    /**
     * @param string $name
     * @return self
     */
    protected function initializing($name)
    {
        if (!empty($this->pending[$name])) {
            return true;
        }

        $this->pending[$name] = true;

        return false;
    }

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return object
     * @throws RuntimeException
     */
    protected function manage(RequestInterface $request, array $options = [])
    {
        $name = $request->name();

        $assigned = $this->assigned($name);
        $config   = $this->configured($name);
        $service  = $this->service($name);

        if (!$config && !$assigned && !$service) {
            return null;
        }

        if ($service && $request->shared()) {
            return $service;
        }

        if ($this->initializing($name)) {
            throw new RuntimeException('Circular dependency: ' . $name);
        }

        $service = $request->service($assigned ? : $this->factory($config), $options);

        if ($request->shared()) {
            $this->add($name, $service);
        }

        $this->initialized($name);

        return $service;
    }

    /**
     * @param array|string $name
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
     * @param string|RequestInterface $request
     * @param bool $shared
     * @return RequestInterface
     */
    protected function request($request, $shared = true)
    {
        return $request instanceof RequestInterface ? $request : new Request($request, $shared);
    }
}
