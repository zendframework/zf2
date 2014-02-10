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
    protected $pending = [];

    /**
     * @param string $name
     * @param string $service
     * @return self
     */
    public function add($name, $service)
    {
        $this[$name] = $service;
        return $this;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        if (!isset($this[$name])) {
            return false;
        }

        return $this[$name];
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
    public function initializing($name)
    {
        $this->pending[$name] = true;
        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function pending($name)
    {
        return !empty($this->pending[$name]);
    }

    /**
     * @param string $name
     * @param mixed $service
     * @return self
     */
    public function set($name, $service)
    {
        $this[$name] = $service;

        $this->pending[$name] = false;

        return $this;
    }
}
