<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

trait ManagerTrait
{
    /**
     *
     */
    use ServiceRequestTrait;

    /**
     * @param $name
     * @param $service
     * @return self
     */
    public function add($name, $service)
    {
        $this->shared[$name] = $service;
        return $this;
    }

    /**
     * @param mixed $name
     * @param array $options
     * @param bool $shared
     * @return false|object
     */
    public function get($name, array $options = [], $shared = true)
    {
        if (is_array($name)) {

            list($name, $options) = $name;

            if (!is_array($options)) {
                $options = [$options];
            }
        }

        return $this->request(new Request($name, $shared), $options);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->shared[$name]);
    }

    /**
     * @return ConfigInterface
     */
    public function services()
    {
        return $this->services;
    }
}
