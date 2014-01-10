<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Exception;
use Zend\Framework\Event\EventInterface;
use Zend\Framework\Service\Listener as ServiceListener;
use Zend\Framework\Service\ListenerInterface as ServiceListenerInterface;


class Listener
    implements ListenerInterface, EventListenerInterface
{
    /**
     *
     */
    use ListenerTrait;

    /**
     * @param ServiceListenerInterface $sm
     */
    public function __construct(ServiceListenerInterface $sm)
    {
        $this->sm = $sm;
    }

    /**
     * @param array $config
     * @return Listener
     * @throws Exception
     */
    public static function init(array $config = [])
    {
        $sm = new ServiceListener;

        $sm->listeners($config['service_manager'])
            ->add('ServiceManager', $sm)
            ->add('ApplicationConfig', $config);

        $application = new Listener($sm);

        $sm->add('EventManager', $application);

        $application->listeners = $config['event_manager']['listeners'];

        //$mm = $sm->get('ModuleManager');
        //$mm->loadModules();

        return $application;
    }

    /**
     * Trigger
     *
     * @param EventInterface $event
     * @return mixed
     */
    public function __invoke(EventInterface $event)
    {
        $name   = $event->name();
        $target = $event->target();

        $result = null;

        foreach($this->queue($name, $target) as $listener) {

            //var_dump($event->name().' :: '.get_class($event).' :: '.get_class($listener));

            $result = $event->__invoke($listener);

            if ($event->stopped()) {
                break;
            }
        }

        return $result;
    }
}
