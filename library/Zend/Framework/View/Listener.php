<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\EventManager\EventInterface;
use Zend\Framework\ServiceManager\FactoryInterface;

class Listener
    implements ListenerInterface, EventListenerInterface, FactoryInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * @param $event
     * @param $target
     * @param $priority
     */
    public function __construct($event = self::EVENT_RENDER, $target = null, $priority = null)
    {
        $this->eventName = $event;
    }

    /**
     * @param EventInterface $event
     * @return mixed|void
     */
    public function __invoke(EventInterface $event)
    {
        switch($event->getEventName())
        {
            case self::EVENT_RENDER:
                $this->selectRenderer($event);
                break;
            case self::EVENT_RESPONSE:
                $this->injectResponse($event);
                break;
        }
    }
}
