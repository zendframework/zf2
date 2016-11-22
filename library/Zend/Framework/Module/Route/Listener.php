<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Module\Route;

use Zend\Framework\Controller\RouteMatch\EventInterface;
use Zend\Mvc\Router\RouteMatch;

class Listener
    implements ListenerInterface
{
    /**
     * @param EventInterface $event
     * @param null $options
     * @return mixed
     */
    public function __invoke(EventInterface $event, $options = null)
    {
        $routeMatch = $event->routeMatch();

        if (!$routeMatch instanceof RouteMatch) {
            // Can't do anything without a route match
            return $routeMatch;
        }

        $module = $routeMatch->getParam(self::MODULE_NAMESPACE, false);
        if (!$module) {
            // No module namespace found; nothing to do
            return $routeMatch;
        }

        $controller = $routeMatch->getParam('controller', false);
        if (!$controller) {
            // no controller matched, nothing to do
            return $routeMatch;
        }

        // Ensure the module namespace has not already been applied
        if (0 === strpos($controller, $module)) {
            return $routeMatch;
        }

        // Keep the originally matched controller name around
        $routeMatch->setParam(self::ORIGINAL_CONTROLLER, $controller);

        // Prepend the controllername with the module, and replace it in the
        // matches
        $controller = $module . '\\' . str_replace(' ', '', ucwords(str_replace('-', ' ', $controller)));
        $routeMatch->setParam('controller', $controller);

        return $routeMatch;
    }
}
