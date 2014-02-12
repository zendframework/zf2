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
use Zend\Framework\Service\Factory\InstanceTrait as ServiceFactory;

trait ManagerTrait
{
    /**
     *
     */
    use ServiceFactory;

    /**
     * @var ConfigInterface
     */
    protected $services;

    /**
     * @param $name
     * @param $service
     * @return self
     */
    public function add($name, $service)
    {
        $this->services->add($name, $service);
        return $this;
    }

    /**
     * @param mixed $name
     * @param array $options
     * @param bool $shared
     * @return false|object
     */
    public function get($name, array $options = [], $shared = true)
    {
        if (is_array($name)) {

            list($name, $options) = $name;

            if (!is_array($options)) {
                $options = [$options];
            }
        }

        return $this->service(new Request($name, $shared), $options);
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
        $services = $this->services;
        $shared   = $request->shared();

        $config  = $services->config($alias);
        $service = $services->get($alias);

        if (!$config && !$service) {
            return false;
        }

        if ($service) {
            return $service;
        }

        if ($services->initializing($alias)) {
            throw new Exception('Circular dependency: '.$alias);
        }

        $instance = $this->instance($config, $request, $options);

        if ($shared) {
            $services->add($alias, $instance);
        }

        $services->initialized($alias);

        return $instance;
    }

    /**
     * @return ConfigInterface
     */
    public function services()
    {
        return $this->services;
    }
}
