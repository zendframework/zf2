<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ModuleWithEvents;

use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\Mvc\MvcEvent;

class Module implements BootstrapListenerInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $application = $e->getApplication();
        $events      = $application->getEventManager();
        $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), -1000);
    }

    public function onRoute(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        if ($routeMatch->getMatchedRouteName() == "myroutebis") {
            return;
        }

        $application = $e->getApplication();
        $events      = $application->getEventManager()->getSharedManager();
        $events->attach('Zend\Mvc\Application', MvcEvent::EVENT_FINISH, function ($e) use ($application) {
            $response = $application->getResponse();
            $response->setContent("<html></html>");
        }, 1000000);
    }
}
