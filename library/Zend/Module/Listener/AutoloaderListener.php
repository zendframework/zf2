<?php

namespace Zend\Module\Listener;

use Zend\Loader\AutoloaderFactory;
use Zend\Module\Consumer\AutoloaderProvider;
use Zend\Module\ModuleEvent;

class AutoloaderListener extends AbstractListener
{
    public function __invoke(ModuleEvent $e)
    {
        $module = $e->getModule();
        if (!$module instanceof AutoloaderProvider) {
            return;
        }
        $autoloaderConfig = $module->getAutoloaderConfig();
        AutoloaderFactory::factory($autoloaderConfig);
    }
}
