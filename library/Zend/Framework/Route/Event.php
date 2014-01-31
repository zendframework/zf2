<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
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
     * @var string
     */
    protected $name = self::EVENT_ROUTE;

    /**
     * @param ListenerInterface $listener
     * @param null $options
     * @return mixed
     */
    public function trigger(ListenerInterface $listener, $options = null)
    {
        $response = $listener->trigger($this, $options);

        if ($response instanceof RouteMatch) {
            $this->setRouteMatch($response);
        }

        return $response;
    }
}
