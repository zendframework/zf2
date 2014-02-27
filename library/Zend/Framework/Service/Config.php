<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

class Config
    implements ConfigInterface
{
    /**
     * @var array
     */
    protected $assigned = [];

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var array
     */
    protected $pending = [];

    /**
     * @var array
     */
    protected $service = [];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @param string $name
     * @param string $service
     * @return self
     */
    public function add($name, $service)
    {
        $this->service[$name] = $service;
        return $this;
    }

    /**
     * @param string $name
     * @param callable $factory
     * @return self
     */
    public function assign($name, callable $factory)
    {
        $this->assigned[$name] = $factory;
        return $this;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function assigned($name)
    {
        return isset($this->assigned[$name]) ? $this->assigned[$name] : null;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function config($name)
    {
        return isset($this->config[$name]) ? $this->config[$name] : null;
    }

    /**
     * @return array
     */
    public function configuration()
    {
        return $this->config;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        return isset($this->service[$name]) ? $this->service[$name] : null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->service[$name]);
    }

    /**
     * @param $name
     * @return self
     */
    public function initialized($name)
    {
        $this->pending[$name] = false;
        return $this;
    }

    /**
     * @param $name
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
     * @return string|void
     */
    public function serialize()
    {
        return serialize($this->config);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->__construct(unserialize($serialized));
    }
}
