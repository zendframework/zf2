<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller;

use Zend\Framework\EventManager\EventTrait as Event;
use Zend\Framework\Response\ServiceTrait as Response;
use Zend\Framework\View\Model\ServiceTrait as ViewModel;
use Zend\Framework\EventManager\ResultTrait as Result;
use Zend\Framework\EventManager\ErrorTrait as Error;

trait EventTrait
{
    /**
     *
     */
    use Error,
        Event,
        Response,
        Result,
        ViewModel;
}
