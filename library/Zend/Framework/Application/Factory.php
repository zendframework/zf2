<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\Application\Config\Config;
use Zend\Framework\Event\Manager\ManagerInterface as EventManagerInterface;

class Factory
{
    /**
     * @param Config $config
     * @return EventManagerInterface
     */
    public static function factory(Config $config)
    {
        $services = $config->services();

        $application = new Manager($services, $config->listeners());

        $services->add('Config', $config)
                 ->add('EventManager', $application);

        return $application;
    }
}
