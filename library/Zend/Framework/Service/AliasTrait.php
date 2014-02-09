<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

trait AliasTrait
{
    /**
     * @var array
     */
    protected $alias = [];

    /**
     * @param $name
     * @return string
     */
    public function alias($name)
    {
        $name = strtolower($name);

        if (isset($this->alias[$name])) {
            return $this->alias[$name];
        }

        return $name;
    }

    /**
     * @param string $name
     * @param mixed $service
     */
    public function configure($name, $service)
    {
        $this->sm->configure($this->alias($name), $service);
    }

    /**
     * @param string $name
     * @param array $options
     * @return false|object
     */
    public function get($name, array $options = [])
    {
        return $this->sm->get($this->alias($name), $options);
    }

    /**
     * @param $name
     * @param $service
     * @return self
     */
    public function add($name, $service)
    {
        return $this->sm->add($this->alias($name), $service);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return $this->sm->has($this->alias($name));
    }
}
