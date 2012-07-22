<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ModuleManager
 */

namespace Zend\ModuleManager\Listener;

use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\Feature\ServiceManagerProviderInterface;

/**
 * Service manager trigger
 *
 * @category   Zend
 * @package    Zend_ModuleManager
 * @subpackage Listener
 */
class ServiceManagerTrigger extends AbstractListener
{
    /**
     * @param  ModuleEvent $e
     * @return void
     */
    public function __invoke(ModuleEvent $e)
    {
        $module = $e->getModule();
        if (!$module instanceof ServiceManagerProviderInterface
            && !method_exists($module, 'addServiceManager')
        ) {
            return;
        }

        $serviceManager = $e->getParam('ServiceManager');

        if ($serviceManager === null) {
            throw new Exception\RuntimeException('Unable to access ServiceManager via ModuleEvent.');
        }

        $module->addServiceManager($serviceManager->get('ServiceListener'));
    }
}
