<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Match;

use Zend\Framework\Event\EventInterface as Event;
use Zend\Mvc\Router\RouteMatch;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;

interface EventInterface
    extends Event
{
    /**
     *
     */
    const EVENT = 'Controller\Match';

    /**
     * @return RouteMatch
     */
    public function routeMatch();
}
