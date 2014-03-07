<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route;

use Zend\Mvc\Router\RouteMatch as RouteMatch;
use Zend\Framework\Route\ServiceTrait as Router;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use Router;

    /**
     * @param EventInterface $event
     * @param mixed $request
     * @return RouteMatch
     */
    public function __invoke(EventInterface $event, $request)
    {
        return $this->match($request);
    }
}
