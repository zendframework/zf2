<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use Zend\Framework\Application\Config\ConfigInterface as ApplicationConfigInterface;
use Zend\Framework\Config\ConfigTrait;

trait ConfigServiceTrait
{
    /**
     *
     */
    use ConfigTrait;

    /**
     * @var ConfigInterface
     */
    protected $services;

    /**
     * @param string $name
     * @param mixed $service
     * @return self
     */
    public function add($name, $service)
    {
        $this->services->add($name, $service);
        return $this;
    }

    /**
     * @param string $name
     * @return object|null
     */
    public function added($name)
    {
        return $this->services->added($name);
    }

    /**
     * @param string $name
     * @param callable $callable
     * @return $this
     */
    public function assign($name, callable $callable)
    {
        $this->services->assign($name, $callable);
        return $this;
    }

    /**
     * @param string $name
     * @return callable|null
     */
    public function assigned($name)
    {
        return $this->services->assigned($name);
    }

    /**
     * @return ApplicationConfigInterface
     */
    public function config()
    {
        return $this->config;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function configured($name)
    {
        return $this->services->configured($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return $this->services->has($name);
    }
}
