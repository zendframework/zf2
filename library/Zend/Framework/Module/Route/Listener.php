<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Module\Route;

use Zend\Framework\EventManager\EventInterface as Event;
use Zend\Framework\EventManager\Listener as ParentListener;
use Zend\Framework\Route\EventInterface as RouteInterface;
use Zend\Framework\ServiceManager\FactoryInterface;
use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Mvc\Router\Http\RouteMatch;

class Listener
    extends ParentListener
    implements FactoryInterface,
               RouteInterface
{
    /**
     *
     */
    const MODULE_NAMESPACE    = '__NAMESPACE__';

    /**
     *
     */
    const ORIGINAL_CONTROLLER = '__CONTROLLER__';

    /**
     * @var string
     */
    protected $eventName = self::EVENT_ROUTE;

    /**
     * @param ServiceManager $sm
     * @return Listener
     */
    public function createService(ServiceManager $sm)
    {
        return $this;
    }

    /**
     * @param Event $event
     * @return void
     */
    public function __invoke(Event $event)
    {
        $matches = $event->getRouteMatch();
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
