<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

class Event
    implements EventInterface, EventListenerInterface
{
    /**
     *
     */
    use EventTrait {
        EventTrait::__construct as event;
    }

    /**
     * @param string $name
     * @param string $target
     */
    public function __construct($name = self::EVENT_DISPATCH, $target = null)
    {
        $this->event($name, $target);
    }
}
