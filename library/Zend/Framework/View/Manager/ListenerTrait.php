<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Manager;

use Zend\Framework\EventManager\ListenerTrait as Listener;
use Zend\Framework\Service\ServicesTrait as Services;
use Zend\Framework\Service\ListenerConfigInterface as Config;
use Zend\Framework\View\Config as ViewConfig;

trait ListenerTrait
{
    /**
     *
     */
    use Listener, Services;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @return ViewConfig
     */
    public function viewConfig()
    {
        return $this->config;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function config($name)
    {
        return $this->config->get($name);
    }

    /**
     * @return array
     */
    public function viewHelpers()
    {
        return $this->config->viewHelpers();
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
}
