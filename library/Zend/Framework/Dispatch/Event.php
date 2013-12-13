<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

use Zend\Framework\MvcEvent;
use Zend\Framework\EventManager\ListenerInterface as EventListener;


class Event
    extends MvcEvent
{
    /**
     * @var string
     */
    protected $name = MvcEvent::EVENT_DISPATCH;

    /**
     * @param EventListener $listener
     */
    public function __invoke(EventListener $listener)
    {
        $response = $listener($this);

        if ($response) {
            $this->setResponse($response);
        }

        $this->eventResponses[] = $response;

        if ($this->callback) {
            call_user_func($this->callback, $this, $listener, $response);
        }
    }
}
