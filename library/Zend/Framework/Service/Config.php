<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use ArrayObject;

class Config
    extends ArrayObject
    implements ConfigInterface
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var array
     */
    protected $pending = [];

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
        $this[$name] = $service;

        $this->pending[$name] = false;

        return $this;
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
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        return isset($this[$name]) ? $this[$name] : null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return !empty($this[$name]);
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
        $this->pending = [];

        $this->exchangeArray([]);

        return serialize($this->config);
    }

    /**
     * @param string $serialized
     * @return void|Config
     */
    public function unserialize($serialized)
    {
        return new self(unserialize($serialized));
    }
}
