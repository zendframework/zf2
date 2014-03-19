<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Event\Config;

use Zend\Framework\Config\ConfigTrait as Config;

trait ConfigTrait
{
    /**
     *
     */
    use Config;

    /**
     * @param string $name
     * @param string|callable $listener
     * @param $priority
     * @return self
     */
    public function add($name, $listener, $priority = self::PRIORITY)
    {
        if (!isset($this->config[$name])) {
            $this->config[$name] = [];
        }

        $this->config[$name][$priority][] = $listener;

        return $this;
    }

    /**
     * @param $name
     * @return array
     */
    public function queue($name)
    {
        if (!isset($this->config[$name])) {
            return [];
        }

        ksort($this->config[$name], SORT_NUMERIC);

        return $this->config[$name];
    }

    /**
     * @param string|callable $listener
     * @return self
     */
    public function remove($listener)
    {
        foreach($this->config as $name => $listeners) {
            foreach(array_keys($listeners) as $priority) {
                $this->config[$name][$priority] = array_diff($this->config[$name][$priority], [$listener]);
            }
        }

        return $this;
    }
}
