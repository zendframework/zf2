<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework;

use Zend\Framework\ServiceManager\ServiceLocatorInterface as ServiceManager;
use Zend\Framework\EventManager\ManagerInterface as EventManager;

interface ApplicationInterface
{
    /**
     * @return EventManager
     */
    public function getEventManager();

    /**
     * @return ServiceManager
     */
    public function getServiceManager();

    /**
     * @param array $config
     * @return self
     */
    public static function init(array $config = []);

    /**
     * Run the application
     *
     * @return void
     */
    public function run();
}
