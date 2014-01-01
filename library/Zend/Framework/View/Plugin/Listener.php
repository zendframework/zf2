<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Plugin;

use Zend\Framework\Service\ListenerInterface as ServiceManager;

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
     * @param array $alias
     * @param ServiceManager $sm
     * @param string $event
     * @param null $target
     * @param null $priority
     */
    public function __construct(array $alias, ServiceManager $sm, $event = self::EVENT_VIEW_PLUGIN, $target = null, $priority = null)
    {
        $this->listener($event, $target, $priority);
        $this->sm = $sm;
        $this->alias = $alias;
    }
}
