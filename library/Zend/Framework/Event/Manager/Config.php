<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event\Manager;

use Serializable;
use Zend\Framework\Event\ListenerInterface as Listener;

class Config
    implements ConfigInterface, Serializable
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var array
     */
    protected $listener = [];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config   = $config;
        $this->listener = $config;
    }

    /**
     * @param string $name
     * @param string|Listener $listener
     * @param $priority
     * @return self
     */
    public function add($name, $listener, $priority = self::PRIORITY)
    {
        if (!isset($this->listener[$name])) {
            $this->listener[$name] = [];
        }

        $this->listener[$name][$priority][] = $listener;

        return $this;
    }

    /**
     * @param string $name
     * @return array
     */
    public function get($name)
    {
        if (!isset($this->listener[$name])) {
            return [];
        }

        ksort($this->listener[$name], SORT_NUMERIC);

        return $this->listener[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->listener[$name]);
    }

    /**
     * @param string|Listener $listener
     * @return self
     */
    public function remove($listener)
    {
        foreach($this as $name => $listeners) {
            foreach(array_keys($listeners) as $priority) {
                $this->listener[$name][$priority] = array_diff($this->listener[$name][$priority], [$listener]);
            }
        }

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
     * @return void|Config
     */
    public function unserialize($serialized)
    {
        $this->config = $this->listener = unserialize($serialized);
    }
}
