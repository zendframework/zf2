<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Match;

use Zend\Mvc\Router\RouteMatch as RouteMatch;
use Zend\Framework\Route\Manager\ServiceTrait as RouteManager;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use RouteManager;

    /**
     * @param EventInterface $event
     * @param mixed $request
     * @return RouteMatch
     */
    public function __invoke(EventInterface $event, $request)
    {
        return $this->match($event->request());
    }
}
