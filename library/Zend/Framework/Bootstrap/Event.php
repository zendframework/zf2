<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Bootstrap;

use Zend\Framework\ApplicationServiceTrait as ServiceTrait;
use Zend\Framework\Bootstrap\EventInterface as BootstrapInterface;
use Zend\Framework\EventManager\Event as EventClass;

class Event
    extends EventClass
    implements BootstrapInterface
{
    /**
     *
     */
    use ServiceTrait;

    /**
     * @var string
     */
    protected $eventName = self::EVENT_BOOTSTRAP;
}
