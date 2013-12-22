<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Renderer;

use Zend\Framework\EventManager\Event as EventClass;
use Zend\Framework\View\Renderer\EventInterface as ViewRendererEvent;

class Event
    extends EventClass
    implements ViewRendererEvent
{
    /**
     * @var
     */
    protected $eventName = self::EVENT_RENDERER;
}
