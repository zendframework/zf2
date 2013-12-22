<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route;

use Zend\Framework\ApplicationServiceTrait as ServiceTrait;
use Zend\Framework\EventManager\Event as EventClass;
use Zend\Framework\Route\EventInterface as RouteInterface;

class Event
    extends EventClass
    implements RouteInterface
{
    /**
     *
     */
    use ServiceTrait;

    /**
     * @var string
     */
    protected $eventName = self::EVENT_ROUTE;
}
