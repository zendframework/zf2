<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Config;

trait ConfigTrait
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @param string $name
     * @param mixed $config
     * @return self
     */
    public function add($name, $config)
    {
        $this->config[$name] = $config;
        return $this;
    }

    /**
     * @return array
     */
    public function config()
    {
        return $this->config;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        return isset($this->config[$name]) ? $this->config[$name] : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->config[$name]);
    }

    /**
     * @param string $name
     * @return self
     */
    public function remove($name)
    {
        unset($this->config[$name]);
        return $this;
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
     * @return void|ConfigInterface
     */
    public function unserialize($serialized)
    {
        $this->__construct(unserialize($serialized));
    }
}
