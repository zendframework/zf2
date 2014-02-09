<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Route;

use Zend\Framework\Application\EventInterface;
use Zend\Framework\Event\ListenerTrait as EventListener;
use Zend\Framework\Event\Manager\ServiceTrait as EventManager;
use Zend\Framework\Request\ServiceTrait as Request;
use Zend\Framework\Route\ServicesTrait as Route;
use Zend\Framework\Service\ServiceTrait as Service;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use EventListener,
        EventManager,
        Request,
        Route,
        Service;

    /**
     * @param EventInterface $event
     * @param $options
     * @return mixed
     */
    public function __invoke(EventInterface $event, $options = null)
    {
        $routeMatch = $this->trigger('Route\Event', $this->request);

        //update service manager, needed for render (i.e url view helper)
        $this->setRouteMatch($routeMatch);

        return $routeMatch;
    }
}
