<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ModuleManager\Listener;

use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\ModuleEventListener as EventListener;
use Zend\Framework\EventManager\EventInterface;
use Zend\Framework\MvcEvent;
use Zend\Framework\EventManager\CallbackListener;

/**
 * Autoloader listener
 */
class OnBootstrapListener extends EventListener
{

    /**
     * @param  EventInterface $event
     * @return void
     */
    public function __invoke(EventInterface $event)
    {
        $module = $event->getModule();
        if (!$module instanceof BootstrapListenerInterface
            && !method_exists($module, 'onBootstrap')
        ) {
            return;
        }

        $moduleManager = $event->getTarget();
        $events        = $moduleManager->getEventManager();
        $sharedEvents  = $events->getSharedManager();
        $sharedEvents->attach(new CallbackListener(array($module, 'onBootstrap'), MvcEvent::EVENT_BOOTSTRAP, 'Zend\Mvc\Application'));
    }
}
