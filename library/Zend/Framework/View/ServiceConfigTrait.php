<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

trait ServiceConfigTrait
{
    /**
     * @var
     */
    protected $config;

    /**
     * @return array
     */
    public function aliases()
    {
        return $this->config->aliases();
    }

    /**
     * @return string
     */
    public function layoutTemplate()
    {
        return $this->config->layoutTemplate();
    }

    /**
     * @return bool
     */
    public function displayExceptions()
    {
        return $this->config->displayExceptions();
    }

    /**
     * @return bool
     */
    public function displayNotFoundReason()
    {
        return $this->config->displayNotFoundReason();
    }

    /**
     * @return string
     */
    public function exceptionTemplate()
    {
        return $this->config->exceptionTemplate();
    }

    /**
     * @return string
     */
    public function notFoundTemplate()
    {
        return $this->config->notFoundTemplate();
    }

    /**
     * @return ConfigInterface
     */
    public function viewConfig()
    {
        return $this->config;
    }
}
