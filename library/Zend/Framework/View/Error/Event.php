<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Error;

use Zend\Framework\Event\ErrorTrait as Error;
use Zend\Framework\Event\ExceptionTrait as Exception;
use Zend\Framework\Event\ResultTrait as Result;
use Zend\Framework\Event\EventTrait as EventTrait;

class Event
    implements EventInterface
{
    /**
     *
     */
    use Error,
        EventTrait,
        Exception,
        Result;
}
