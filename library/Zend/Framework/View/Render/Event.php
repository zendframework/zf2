<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Render;

use Zend\Framework\EventManager\Event as EventClass;
use Zend\Framework\View\Render\EventInterface as ViewRenderEvent;

class Event
    extends EventClass
    implements ViewRenderEvent
{
    /**
     * @var
     */
    protected $eventName = self::EVENT_RENDER;
}
