<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\Application\Config\ServicesTrait as Config;
use Zend\Framework\Event\Manager\ServicesTrait as EventManager;
use Zend\Framework\Event\Manager\ManagerInterface as EventManagerInterface;
use Zend\Framework\Service\Manager as ServiceManager;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Factory\Factory;

class ApplicationFactory
    extends Factory
{
    /**
     *
     */
    use Config,
        EventManager;

    /**
     * @param array $config
     * @return EventManagerInterface
     */
    public static function factory(array $config)
    {
        return (new ServiceManager)->config($config['service_manager'])
                                   ->add('AppConfig', $config)
                                   ->get('EventManager');
    }

    /**
     * @param Request $request
     * @param array $options
     * @return Listener
     */
    public function service(Request $request, array $options = [])
    {
        return (new Application($this->sm))->config($this->appConfig()['event_manager']['listeners']);
    }
}
