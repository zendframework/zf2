<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\View;

use Zend\Framework\Application\EventInterface;
use Zend\Framework\Application\EventListenerInterface;
use Zend\Framework\Event\ListenerTrait as ListenerTrait;
use Zend\Framework\Event\Manager\ServiceTrait as EventManager;
use Zend\Framework\View\Event as View;

class Listener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use EventManager,
        ListenerTrait;

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
     * @param $response
     * @return mixed
     */
    public function trigger(EventInterface $event, $response)
    {
        return $this->em->trigger(new View, $response);
    }
}
