<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Module\Route;

use Zend\Framework\MvcEvent;
use Zend\Framework\EventManager\Listener as EventListener;
use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\ServiceManager\ServiceRequest;
use Zend\Framework\ServiceManager\FactoryInterface;

class Listener
    extends EventListener
    implements FactoryInterface
{
    const MODULE_NAMESPACE    = '__NAMESPACE__';
    const ORIGINAL_CONTROLLER = '__CONTROLLER__';

    protected $name = MvcEvent::EVENT_ROUTE;

    public function createService(ServiceManager $sm)
    {
        return new self();
    }

    public function __invoke(Event $e)
    {
        $matches = $e->getRouteMatch();
        if (!$matches instanceof RouteMatch) {
            // Can't do anything without a route match
            return;
        }

        $module = $matches->getParam(self::MODULE_NAMESPACE, false);
        if (!$module) {
            // No module namespace found; nothing to do
            return;
        }

        $controller = $matches->getParam('controller', false);
        if (!$controller) {
            // no controller matched, nothing to do
            return;
        }

        // Ensure the module namespace has not already been applied
        if (0 === strpos($controller, $module)) {
            return;
        }

        // Keep the originally matched controller name around
        $matches->setParam(self::ORIGINAL_CONTROLLER, $controller);

        // Prepend the controllername with the module, and replace it in the
        // matches
        $controller = $module . '\\' . str_replace(' ', '', ucwords(str_replace('-', ' ', $controller)));
        $matches->setParam('controller', $controller);
    }
}
