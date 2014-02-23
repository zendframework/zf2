<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller;

class Config
    implements ConfigInterface
{
    /**
     * @var array
     */
    protected $serial = [];

    /**
     * @var array
     */
    protected $listener = [];

    /**
     * @param array $listener
     */
    public function __construct(array $listener = [])
    {
        $this->listener = $listener;
        $this->serial   = $listener;
    }

    /**
     * @param string $name
     * @param string|callable $listener
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
     * @param string|callable $listener
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
        return serialize($this->serial);
    }

    /**
     * @param string $serialized
     * @return void|Config
     */
    public function unserialize($serialized)
    {
        $this->serial = $this->listener = unserialize($serialized);
    }
}
