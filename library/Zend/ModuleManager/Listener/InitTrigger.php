<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ModuleManager\Listener;

use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\ModuleEventListener as EventListener;
use Zend\Framework\EventManager\EventInterface;

/**
 * Init trigger
 */
class InitTrigger extends EventListener
{
    /**
     * @param EventInterface $event
     * @return void
     */
    public function __invoke(EventInterface $event)
    {
        $module = $e->getModule();
        if (!$module instanceof InitProviderInterface
            && !method_exists($module, 'init')
        ) {
            return;
        }

        $module->init($e->getTarget());
    }
}
