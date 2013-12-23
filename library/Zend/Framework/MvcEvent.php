<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework;

use Zend\Framework\ApplicationServiceTrait as ServiceTrait;
use Zend\Framework\EventManager\EventTrait as EventTrait;

class MvcEvent
    implements MvcEventInterface
{
    /**
     *
     */
    use EventTrait, ServiceTrait;

    /**
     * @var string
     */
    protected $eventName = self::EVENT_MVC_APPLICATION;

    /**
     * Target (identifiers) of the events to listen for
     *
     * @var mixed
     */
    protected $eventTarget = self::WILDCARD;

    /**
     * @var bool Whether or not to stop propagation
     */
    protected $eventStopPropagation = false;
}
