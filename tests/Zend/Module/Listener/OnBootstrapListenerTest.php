<?php

namespace ZendTest\Module\Listener;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Loader\ModuleAutoloader,
    Zend\Loader\AutoloaderFactory,
    Zend\Mvc\MvcEvent,
    Zend\Module\Listener\OnBootstrapListener,
    Zend\Module\Listener\ModuleResolverListener,
    Zend\Module\Listener\ListenerOptions,
    Zend\Module\Manager,
    Zend\Mvc\Application,
    Zend\Config\Config,
    Zend\EventManager\EventManager,
    Zend\EventManager\SharedEventManager,
    ZendTest\Module\TestAsset\MockApplication;

class OnBootstrapListenerTest extends TestCase
{

    public function setUp()
    {
        $this->loaders = spl_autoload_functions();
        if (!is_array($this->loaders)) {
            // spl_autoload_functions does not return empty array when no
            // autoloaders registered...
            $this->loaders = array();
        }

        // Store original include_path
        $this->includePath = get_include_path();

        $autoloader = new ModuleAutoloader(array(
            dirname(__DIR__) . '/TestAsset',
        ));
        $autoloader->register();

        $sharedEvents = new SharedEventManager();
        $this->moduleManager = new Manager(array());
        $this->moduleManager->events()->setSharedManager($sharedEvents);
        $this->moduleManager->events()->attach('loadModule.resolve', new ModuleResolverListener, 1000);
        $this->moduleManager->events()->attach('loadModule', new OnBootstrapListener, 1000);

        $this->application = new MockApplication;
        $events            = new EventManager(array('Zend\Mvc\Application', 'ZendTest\Module\TestAsset\MockApplication', 'application'));
        $events->setSharedManager($sharedEvents);
        $this->application->setEventManager($events);
    }

    public function tearDown()
    {
        // Restore original autoloaders
        AutoloaderFactory::unregisterAutoloaders();
        $loaders = spl_autoload_functions();
        if (is_array($loaders)) {
            foreach ($loaders as $loader) {
                spl_autoload_unregister($loader);
            }
        }

        foreach ($this->loaders as $loader) {
            spl_autoload_register($loader);
        }

        // Restore original include_path
        set_include_path($this->includePath);
    }

    public function testOnBootstrapMethodCalledByOnBootstrapListener()
    {
        $moduleManager = $this->moduleManager;
        $moduleManager->setModules(array('ListenerTestModule'));
        $moduleManager->loadModules();
        $this->application->bootstrap();
        $modules = $moduleManager->getLoadedModules();
        $this->assertTrue($modules['ListenerTestModule']->onBootstrapCalled);
    }
}
