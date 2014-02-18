<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\RouteMatch;

use Zend\Framework\Event\EventInterface;
use Zend\Framework\Dispatch\ServicesTrait as Dispatch;
use Zend\Framework\Service\ServiceTrait as ServiceManager;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use Dispatch,
        ServiceManager;

    /**
     * @param EventInterface $event
     * @param null $options
     * @return mixed
     */
    public function __invoke(EventInterface $event, $options = null)
    {
        return $this->controller($event->routeMatch()->getParam('controller'), $event->routeMatch());
    }
}
