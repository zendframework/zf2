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
use Zend\Framework\Event\Manager\ListenerTrait as Listener;
use Zend\Framework\Service\Listener as ServiceListener;

trait ListenerTrait
{
    /**
     *
     */
    use Listener;

    /**
     * @param array $config
     * @return Listener
     * @throws Exception
     */
    public static function init(array $config = [])
    {
        $sm = new ServiceListener;

        $sm->config($config['service_manager'])
           ->add('ServiceManager', $sm)
           ->add('ApplicationConfig', $config);

        return $sm->get('EventManager');
    }

    /**
     * Pull listener from service manager
     *
     * @param $listener
     * @return mixed
     */
    public function listener($listener)
    {
        return $this->sm->get($listener);
    }

    /**
     *
     */
    public function run()
    {
        $event = new Event;

        $event->setServiceManager($this->sm);

        $this->__invoke($event);
    }
}
