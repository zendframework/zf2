<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event\Manager;

use ArrayObject;
use Zend\Framework\Event\ListenerInterface as Listener;

class Config
    extends ArrayObject
    implements ConfigInterface
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
        parent::__construct($config);

        $this->config = $config;
    }

    /**
     * @param string $name
     * @param string|Listener $listener
     * @param $priority
     * @return self
     */
    public function add($name, $listener, $priority = self::PRIORITY)
    {
        if (!isset($this[$name])) {
            $this[$name] = [];
        }

        $this[$name][$priority][] = $listener;

        return $this;
    }

    /**
     * @param string $name
     * @return array
     */
    public function get($name)
    {
        return isset($this[$name]) ? $this[$name] : [];
    }

    /**
     * Push listener to top of queue
     *
     * @param string $name
     * @param string|Listener $listener
     * @param int $priority
     * @return self
     */
    public function push($name, $listener, $priority = self::PRIORITY)
    {
        if (!isset($this[$name])) {
            $this[$name] = [];
        }

        if (!isset($this[$name][$priority])) {
            $this[$name][$priority][] = $listener;
            return $this;
        }

        array_unshift($this[$name][$priority], $listener);

        return $this;
    }

    /**
     * @param string|Listener $listener
     * @return self
     */
    public function remove($listener)
    {
        foreach($this as $name => $listeners) {
            foreach(array_keys($listeners) as $priority) {
                $this[$name][$priority] = array_diff($this[$name][$priority], [$listener]);
            }
        }

        return $this;
    }

    /**
     * @return string|void
     */
    public function serialize()
    {
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
