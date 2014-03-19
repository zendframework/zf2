<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\RouteMatch;

use Zend\Framework\Event\EventTrait as EventTrait;
use Zend\Framework\Route\Match\ServiceTrait as RouteMatchTrait;
use Zend\Mvc\Router\RouteMatch as RouteMatchInterface;

class Event
    implements EventInterface
{
    /**
     *
     */
    use EventTrait,
        RouteMatchTrait;

    /**
     * @param RouteMatchInterface $routeMatch
     */
    public function __construct(RouteMatchInterface $routeMatch)
    {
        $this->routeMatch = $this->source = $routeMatch;
    }

    /**
     * @param callable $listener
     * @param null $options
     * @return mixed
     */
    public function __invoke(callable $listener, $options = null)
    {
        return $listener($this, $options);
    }
}
