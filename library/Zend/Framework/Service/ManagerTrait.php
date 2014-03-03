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

trait ManagerTrait
{
    /**
     *
     */
    use ConfigServicesTrait;

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
     * @param string|RequestInterface $request
     * @param bool $shared
     * @return RequestInterface
     */
    protected function request($request, $shared = true)
    {
        return $request instanceof RequestInterface ? $request : new Request($request, $shared);
    }

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return object
     * @throws Exception
     */
    protected function service(RequestInterface $request, array $options = [])
    {
        $alias    = $request->alias();
        $assigned = $this->assigned($alias);
        $config   = $this->configured($alias);
        $service  = $this->added($alias);

        if (!$config && !$assigned && !$service) {
            return null;
        }

        if ($request->shared() && $service) {
            return $service;
        }

        if ($this->initializing($alias)) {
            throw new Exception('Circular dependency: ' . $alias);
        }

        $service = $request->service($assigned ? : $this->factory($config), $options);

        if ($request->shared()) {
            $this->add($alias, $service);
        }

        $this->initialized($alias);

        return $service;
    }
}
