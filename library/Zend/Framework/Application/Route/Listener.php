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
use Zend\Framework\Application\EventListenerInterface;
use Zend\Framework\Route\Event as Route;

class Listener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * @var string
     */
    protected $name = self::EVENT_APPLICATION;

    /**
     * Target
     *
     * @var mixed
     */
    protected $target = self::WILDCARD;

    /**
     * @param EventInterface $event
     * @param $routeMatch
     * @return mixed
     */
    public function trigger(EventInterface $event, $routeMatch)
    {
        $routeMatch = $this->em->trigger(new Route, $this->request);

        //needed for render
        $this->sm->add('Route\Match', $routeMatch);

        return $routeMatch;
    }
}
