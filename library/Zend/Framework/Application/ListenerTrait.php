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
use Zend\Framework\Event\Manager\ManagerTrait as EventManager;
use Zend\Framework\Service\Manager as ServiceManager;

trait ListenerTrait
{
    /**
     *
     */
    use EventManager;

    /**
     * @param array $config
     * @return Listener
     * @throws Exception
     */
    public static function init(array $config = [])
    {
        $sm = new ServiceManager;

        $sm->config($config['service_manager'])
           ->add('ServiceManager', $sm)
           ->add('AppConfig', $config);

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
        $this->trigger(new Event);
        return $this;
    }
}
