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
     * @var array
     */
    protected $shared = [];

    /**
     * @param string $name
     * @param string $service
     * @return self
     */
    public function add($name, $service)
    {
        $this->shared[$name] = $service;
        $this->pending[$name] = false;
        return $this;
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function config($name, $default = null)
    {
        if (isset($this[$name])) {
            return $this[$name];
        }

        return $default;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        if (isset($this->shared[$name])) {
            return $this->shared[$name];
        }

        return null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return !empty($this->shared[$name]);
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
}
