<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\View;

use Zend\Framework\Application\EventInterface as ApplicationEvent;
use Zend\Framework\Event\ListenerTrait as EventListener;
use Zend\Framework\Event\Manager\ServiceTrait as EventManager;
use Zend\Framework\View\Event as View;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use EventManager,
        EventListener;

    /**
     * @param ApplicationEvent $event
     * @param $options
     * @return mixed
     */
    public function trigger(ApplicationEvent $event, $options = null)
    {
        return $this->em->trigger(new View, $event->viewModel());
    }
}
