<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\Event\Manager\ServicesTrait as EventManager;
use Zend\Framework\Event\EventTrait as Event;
use Zend\Framework\Event\ResultTrait as Result;
use Zend\Framework\Request\ServicesTrait as Request;
use Zend\Framework\Response\ServicesTrait as Response;
use Zend\Framework\Route\ServicesTrait as Router;
use Zend\Framework\Service\ServiceTrait as Service;
use Zend\Framework\View\Model\ServicesTrait as ViewModel;

trait EventTrait
{
    /**
     *
     */
    use Event,
        EventManager,
        Response,
        Result,
        Request,
        Router,
        Service,
        ViewModel;
}
