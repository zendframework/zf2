<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Service;

//use Zend\Console\Request as Request;
//use Zend\Console\Response as Response;
use Zend\Framework\EventManager\Manager\ServicesTrait as EventManager;
use Zend\Framework\Service\ListenerTrait as Listener;
use Zend\Framework\Route\ServicesTrait as Route;
use Zend\Framework\Request\ServicesTrait as Request;
use Zend\Framework\Controller\ServicesTrait as Controller;
use Zend\Framework\View\ServicesTrait as View;
use Zend\Framework\Response\ServicesTrait as Response;

trait ServicesTrait
{
    /**
     *
     */
    use Controller, EventManager, Route, Request, Response, View;

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
