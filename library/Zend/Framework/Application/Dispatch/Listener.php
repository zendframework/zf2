<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Dispatch;

use Zend\Framework\Application\EventInterface;
use Zend\Framework\Dispatch\Event as Dispatch;
use Zend\Framework\Event\ListenerTrait as ListenerTrait;
use Zend\Framework\Event\Manager\ServiceTrait as EventManager;

class Listener
    implements ListenerInterface
{
    /**
     *
     */
    use EventManager,
        ListenerTrait;

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
        return $this->em->trigger(new Dispatch, $routeMatch);
    }
}
