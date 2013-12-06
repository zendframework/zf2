<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ModuleManager\Listener;

use Zend\Loader\AutoloaderFactory;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Framework\EventManager\EventInterface;

/**
 * Autoloader listener
 */
class AutoloaderListener extends Listener
{

    /**
     * @param  EventInterface $event
     * @return void
     */
    public function __invoke(EventInterface $event)
    {
        $module = $event->getModule();
        if (!$module instanceof AutoloaderProviderInterface
            && !method_exists($module, 'getAutoloaderConfig')
        ) {
            return;
        }
        $autoloaderConfig = $module->getAutoloaderConfig();
        AutoloaderFactory::factory($autoloaderConfig);
    }
}
