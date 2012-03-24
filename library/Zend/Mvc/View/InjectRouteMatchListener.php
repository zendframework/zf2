<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mvc\View;

use Zend\EventManager\EventCollection,
    Zend\EventManager\ListenerAggregate,
    Zend\Stdlib\Request,
    Zend\Stdlib\ParametersDescription,
    Zend\Mvc\Application,
    Zend\Mvc\MvcEvent,
    Zend\View\Model as ViewModel;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class InjectRouteMatchListener implements ListenerAggregate
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Attach the aggregate to the specified event manager
     * 
     * @param  EventCollection $events 
     * @return void
     */
    public function attach(EventCollection $events)
    {
        $this->listeners[] = $events->attach('dispatch', array($this, 'injectRouteMatch'), 90);
    }

    /**
     * Detach listeners
     *
     * @param  EventCollection $events
     * @return void
     */
    public function detach(EventCollection $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Retrieve the RouteMatch from current MvcEvent and inject it into request.
     *
     * @param  MvcEvent $e
     * @return void
     */
    public function injectRouteMatch(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        $request = $e->getRequest();

        if(!$routeMatch instanceof ParametersDescription){
            return;  // cannot inject without valid RouteMatch
        }

        if(!$request instanceof Request){
            return;  // cannot inject on unknown instances
        }

        /* @var $request \Zend\Stdlib\Request */
        $request->setRouteMatch($routeMatch);
    }
}
