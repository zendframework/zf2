<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Module\Route;

use Zend\Framework\Route\Match\EventInterface;
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
        $response = $event->routeMatch();

        if (!$response instanceof RouteMatch) {
            // Can't do anything without a route match
            return $response;
        }

        $module = $response->getParam(self::MODULE_NAMESPACE, false);
        if (!$module) {
            // No module namespace found; nothing to do
            return $response;
        }

        $controller = $response->getParam('controller', false);
        if (!$controller) {
            // no controller matched, nothing to do
            return $response;
        }

        // Ensure the module namespace has not already been applied
        if (0 === strpos($controller, $module)) {
            return $response;
        }

        // Keep the originally matched controller name around
        $response->setParam(self::ORIGINAL_CONTROLLER, $controller);

        // Prepend the controllername with the module, and replace it in the
        // matches
        $controller = $module . '\\' . str_replace(' ', '', ucwords(str_replace('-', ' ', $controller)));
        $response->setParam('controller', $controller);

        return $response;
    }
}