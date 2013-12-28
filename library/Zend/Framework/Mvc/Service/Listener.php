<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Mvc\Service;

use Zend\Framework\Service\ListenerConfigInterface;

class Listener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use ListenerTrait {
        ListenerTrait::__construct as listener;
    }

    /**
     * @param ListenerConfigInterface $config
     * @param string $event
     * @param null $target
     * @param null $priority
     */
    public function __construct(ListenerConfigInterface $config, $event = self::EVENT_SERVICE, $target = null, $priority = null)
    {
        $this->config = $config;
        $this->sm = $this;
        $this->listener($event, $target, $priority);
    }
}
