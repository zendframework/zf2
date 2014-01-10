<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Service;

class Listener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use ListenerTrait {
        ListenerTrait::__construct as __listener;
    }

    /**
     * @param $event
     * @param $target
     * @param $priority
     */
    public function __construct($event = self::EVENT_SERVICE, $target = null, $priority = null)
    {
        $this->__listener($event, $target, $priority);
        $this->sm = $this;
    }
}
