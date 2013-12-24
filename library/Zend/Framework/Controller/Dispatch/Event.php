<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Dispatch;

use Zend\Framework\EventManager\ListenerInterface as Listener;

class Event
    implements EventInterface, EventListenerInterface
{
    /**
     *
     */
    use EventTrait;

    /**
     * @param string $name
     * @param string $target
     */
    public function __construct($name = self::EVENT_CONTROLLER_DISPATCH, $target = null)
    {
        $this->eventName = $name;
    }

    /**
     * @param Listener $listener
     * @return bool
     */
    public function __invoke(Listener $listener)
    {
        $response = $listener->triggerEvent($this);

        if ($listener instanceof ListenerInterface) {
            $this->setResult($response);
        }

        return $this->eventStopPropagation;
    }
}
