<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Module\Route;

use Zend\Framework\Route\EventInterface;
use Zend\Mvc\Router\Http\RouteMatch;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * @param EventInterface $event
     * @param mixed $response
     * @return mixed
     */
    public function trigger(EventInterface $event, $response)
    {
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
