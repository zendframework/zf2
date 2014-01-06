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

class CallableListener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * @param ServiceManager $sm
     * @param string|callable $factory
     */
    public function __construct(ServiceManager $sm, $factory)
    {
        $this->sm      = $sm;
        $this->factory = $factory;
    }

    /**
     * @param EventInterface $event
     * @return mixed|object
     */
    public function __invoke(EventInterface $event)
    {
        //fixme!
        $options = $event->options();

        return call_user_func_array($this->factory, [$event]);
    }
}
