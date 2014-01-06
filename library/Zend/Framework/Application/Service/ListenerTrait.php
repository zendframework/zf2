<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Service;

use Zend\Framework\Controller\ServicesTrait as Controller;
use Zend\Framework\Service\EventManager\ServiceTrait as EventManager;
use Zend\Framework\Request\ServicesTrait as Request;
use Zend\Framework\Response\ServicesTrait as Response;
use Zend\Framework\Route\ServicesTrait as Route;
use Zend\Framework\Service\ListenerTrait as Listener;
use Zend\Framework\View\ServicesTrait as View;

trait ListenerTrait
{
    /**
     *
     */
    use Listener,
        ServicesTrait;

    /**
     * @return array
     */
    public function applicationConfig()
    {
        return $this->service('ApplicationConfig');
    }

    /**
     * @param array $config
     * @return self
     */
    public function setApplicationConfig(array $config)
    {
        return $this->add('ApplicationConfig', $config);
    }
}
