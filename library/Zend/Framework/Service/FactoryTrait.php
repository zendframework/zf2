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
use Zend\Framework\Service\Factory\FactoryTrait as FactoryService;

trait FactoryTrait
{
    /**
     *
     */
    use FactoryService;

    /**
     * @var array
     */
    protected $pending = [];

    /**
     * @param string $name
     * @return self
     */
    public function initialized($name)
    {
        $this->pending[$name] = false;
        return $this;
    }

    /**
     * @param string $name
     * @return self
     */
    public function initializing($name)
    {
        if (!empty($this->pending[$name])) {
            return true;
        }

        $this->pending[$name] = true;

        return false;
    }

    /**
     * @param RequestInterface $request
     * @return array
     */
    protected function match(RequestInterface $request)
    {
        $alias = $request->alias();
        $name  = $this->alias($alias);

        return [$name, $alias, $this->configuration($alias), $this->assigned($alias), $this->added($alias)];
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

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return object
     * @throws Exception
     */
    protected function service(RequestInterface $request, array $options = [])
    {
        list($name, $alias, $config, $assigned, $service) = $this->match($request);

        if (!$config && !$assigned && !$service) {
            return null;
        }

        if ($request->shared() && $service) {
            return $service;
        }

        if ($this->initializing($name)) {
            throw new Exception('Circular dependency: [' . $alias . ']::[' . $name . ']');
        }

        $service = $request->service($assigned ? : $this->factory($config), $options);

        if ($request->shared()) {
            $this->add($name, $service);
        }

        $this->initialized($name);

        return $service;
    }
}
