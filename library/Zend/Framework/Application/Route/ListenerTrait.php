<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Route;

use Zend\Framework\Event\ListenerTrait as Listener;
use Zend\Framework\Event\Manager\ServiceTrait as EventManager;
use Zend\Framework\Request\ServiceTrait as Request;
use Zend\Framework\Route\ServiceTrait as Route;
use Zend\Framework\Service\ServiceTrait as Service;

trait ListenerTrait
{
    /**
     *
     */
    use EventManager,
        Listener,
        Request,
        Route,
        Service;
}
