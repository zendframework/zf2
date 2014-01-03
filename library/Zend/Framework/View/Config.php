<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

class Config
    implements ConfigInterface
{
    /**
     * @var array
     */
    public $config = [];

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }

        return $default;
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
     * @param $name
     * @return mixed
     */
    public function config($name)
    {
        return $this->get($name);
    }

    /**
     * @return array
     */
    public function viewHelpers()
    {
        return (array) $this->get('view_helpers');
    }

    /**
     * @return string
     */
    public function layoutTemplate()
    {
        return (string) $this->get('layout_template');
    }

    /**
     * @return bool
     */
    public function displayExceptions()
    {
        return (bool) $this->get('display_exceptions');
    }

    /**
     * @return bool
     */
    public function displayNotFoundReason()
    {
        return (bool) $this->get('display_not_found_reason');
    }

    /**
     * @return string
     */
    public function notFoundTemplate()
    {
        return (string) $this->get('not_found_template');
    }

    /**
     * @return string
     */
    public function exceptionTemplate()
    {
        return (string) $this->get('exception_template');
    }
}
