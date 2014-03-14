<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service\Factory;

use Zend\Framework\Application\Manager\ConfigInterface as ApplicationConfig;
use Zend\Framework\Controller\Manager\ConfigInterface as ControllerConfig;
use Zend\Framework\Response\Manager\ConfigInterface as ResponseConfig;
use Zend\Framework\Route\Manager\ConfigInterface as RouteConfig;
use Zend\Framework\Service\Manager\ConfigInterface as ServiceConfig;
use Zend\Framework\Service\Manager\ManagerInterface;
use Zend\Framework\View\Manager\ConfigInterface as ViewConfig;

trait ServiceTrait
{
    /**
     * @var ManagerInterface
     */
    protected $sm;

    /**
     * @return ApplicationConfig|ControllerConfig|ResponseConfig|RouteConfig|ServiceConfig|ViewConfig
     */
    public function config()
    {
        return $this->sm->config();
    }

    /**
     * @param string $name
     * @param mixed $options
     * @return null|object
     */
    public function create($name, $options = null)
    {
        return $this->sm->create($name, $options);
    }

    /**
     * @param string $name
     * @param mixed $options
     * @param bool $shared
     * @return null|object
     */
    public function get($name, $options = null, $shared = true)
    {
        return $this->sm->get($name, $options, $shared);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return $this->sm->has($name);
    }
}