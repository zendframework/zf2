<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework;

use Zend\Framework\EventManager\Event;

class MvcEvent extends Event
{
    const EVENT_BOOTSTRAP           = 'bootstrap';
    const EVENT_DISPATCH            = 'dispatch';
    const EVENT_CONTROLLER_DISPATCH = 'controller.dispatch';
    const EVENT_DISPATCH_ERROR      = 'dispatch.error';
    const EVENT_FINISH              = 'finish';
    const EVENT_RENDER              = 'render';
    const EVENT_RENDER_ERROR        = 'render.error';
    const EVENT_ROUTE               = 'route';
}
