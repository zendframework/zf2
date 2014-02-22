<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
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
     * @var array
     */
    protected $serial = [];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->serial = $config;
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
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        return isset($this->config[$name]) ? $this->config[$name] : null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return !empty($this->config[$name]);
    }

    /**
     * @return string|void
     */
    public function serialize()
    {
        return serialize($this->serial);
    }

    /**
     * @param string $serialized
     * @return void|Config
     */
    public function unserialize($serialized)
    {
        $this->serial = unserialize($serialized);
    }
}
