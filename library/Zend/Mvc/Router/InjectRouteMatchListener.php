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

namespace Zend\Mvc\Router;

use Zend\EventManager\EventCollection as Events,
    Zend\EventManager\ListenerAggregate,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\Router\RouteMatch;

class InjectRouteMatchListener implements ListenerAggregate
{
    /**
     * Listeners we've registered
     * 
     * @var array
     */
    protected $listeners = array();

    /**
     * Attach listeners
     * 
     * @param  Events $events 
     * @return void
     */
    public function attach(Events $events)
    {
        $this->listeners[] = $events->attach('route', array($this, 'injectRouteMatch'), -100);
    }

    /**
     * Detach listeners
     * 
     * @param  Events $events 
     * @return void
     */
    public function detach(Events $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Set the RouteMatch as shared instance in the DI Locator
     *
     * @param  MvcEvent $e 
     * @return void
     */
    public function injectRouteMatch(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();

        if (!$routeMatch instanceof RouteMatch) {
            return;
        }        

        $im = $e->getParam('application')->getLocator()->instanceManager();
        $className = get_class($routeMatch);
        if (!$im->hasSharedInstance($className)) {
            $im->addSharedInstance($routeMatch, $className);
        }
    }
}
