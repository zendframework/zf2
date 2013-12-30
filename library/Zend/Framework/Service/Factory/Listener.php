<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service\Factory;

use Zend\Framework\Service\EventInterface;
use Zend\Framework\Service\ListenerInterface as ServiceManager;

class Listener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use ListenerTrait {
        ListenerTrait::__invoke as factory;
    }

    /**
     * Constructor
     *
     * @param ServiceManager $sm
     */
    public function __construct(ServiceManager $sm)
    {
        $this->sm = $sm;
    }

    /**
     * @param EventInterface $event
     * @return bool|mixed
     */
    public function __invoke(EventInterface $event)
    {
        return $this->factory($event->factory(), $event->options());
    }
}
