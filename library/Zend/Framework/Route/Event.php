<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route;

use Zend\Framework\Event\EventTrait as EventTrait;
use Zend\Framework\Route\ServiceTrait as Route;
use Zend\Mvc\Router\RouteMatch as RouteMatch;

class Event
    implements EventInterface
{
    /**
     *
     */
    use EventTrait,
        Route;

    /**
     * @param callable $listener
     * @param null $options
     * @return mixed
     */
    public function __invoke(callable $listener, $options = null)
    {
        $response = $listener($this, $options);

        if ($response instanceof RouteMatch) {
            $this->setRouteMatch($response);
        }

        return $response;
    }
}
