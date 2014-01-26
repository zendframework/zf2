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
use Zend\Framework\Route\Event as Route;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function trigger(EventInterface $event)
    {
        $routeMatch = $this->em->trigger(new Route, $this->request);

        //needed for render
        $this->sm->add('Route\Match', $routeMatch);

        return $routeMatch;
    }
}
