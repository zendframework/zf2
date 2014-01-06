<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use Zend\Framework\Event\ListenerInterface;

class Event
    implements EventInterface, EventListenerInterface
{
    /**
     *
     */
    use EventTrait;

    /**
     * @param $service
     * @param array $options
     * @param string $name
     */
    public function __construct($service, array $options = [], $name = self::EVENT_SERVICE)
    {
        $this->service = $service;
        $this->options = $options;
        $this->name    = $name;
    }

    /**
     * Trigger
     *
     * @param ListenerInterface $listener
     * @return bool|callable
     */
    public function __invoke(ListenerInterface $listener)
    {
        return $listener->__invoke($this) ?: false;
    }
}
