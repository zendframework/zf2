<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

use Zend\Framework\EventManager\EventTrait as Event;
use Zend\Framework\EventManager\Manager\ServiceTrait as EventManager;
use Zend\Framework\EventManager\ResultTrait as Result;
use Zend\Framework\Route\ServiceTrait as Route;
use Zend\Framework\View\Model\ServiceTrait as ViewModel;

trait EventTrait
{
    /**
     *
     */
    use Event,
        EventManager,
        Result,
        Route,
        ViewModel;
}
