<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Bootstrap;

class Listener
    implements ListenerInterface, EventListenerInterface
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
    public function __construct($event = self::EVENT_BOOTSTRAP, $target = null, $priority = null)
    {
        $this->eventName = $event;
    }

    /**
     * Invokes listener with the event
     *
     * @param EventInterface $event
     * @return mixed
     */
    public function __invoke(EventInterface $event)
    {
    }
}
