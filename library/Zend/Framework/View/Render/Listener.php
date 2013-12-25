<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Render;

use Zend\Framework\EventManager\EventInterface;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\View\Response\EventListenerInterface as ViewResponse;

class Listener
    implements ListenerInterface, EventListenerInterface, FactoryInterface
{
    /**
     *
     */
    use ListenerTrait {
        ListenerTrait::__construct as listener;
    }

    /**
     * @param $event
     * @param $target
     * @param $priority
     */
    public function __construct($event = self::EVENT_VIEW_RENDER, $target = null, $priority = null)
    {
        $this->listener($event, $target, $priority);
    }

    /**
     * @param EventInterface $event
     * @return mixed|void
     */
    public function __invoke(EventInterface $event)
    {
        switch($event->name())
        {
            case self::EVENT_VIEW_RENDER:
                $this->selectViewRenderer($event);
                break;
            case ViewResponse::EVENT_VIEW_RESPONSE:
                $this->injectResponse($event);
                break;
        }
    }
}
