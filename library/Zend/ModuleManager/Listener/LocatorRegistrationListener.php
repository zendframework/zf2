<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ModuleManager\Listener;

use Zend\Framework\EventManager\Event;
use Zend\Framework\EventManager\EventManagerInterface as EventManager;
use Zend\Framework\EventManager\ListenerAggregateInterface;
use Zend\Framework\EventManager\CallbackListener;
use Zend\ModuleManager\Feature\LocatorRegisteredInterface;
use Zend\ModuleManager\ModuleEvent;
use Zend\Framework\MvcEvent;

/**
 * Locator registration listener
 */
class LocatorRegistrationListener implements
    ListenerAggregateInterface
{
    /**
     * @var array
     */
    protected $modules = array();

    /**
     * @var array
     */
    protected $callbacks = array();

    /**
     * loadModule
     *
     * Check each loaded module to see if it implements LocatorRegistered. If it
     * does, we add it to an internal array for later.
     *
     * @param  ModuleEvent $e
     * @return void
     */
    public function onLoadModule(ModuleEvent $e)
    {
        if (!$e->getModule() instanceof LocatorRegisteredInterface) {
            return;
        }
        $this->modules[] = $e->getModule();
    }

    /**
     * loadModules
     *
     * Once all the modules are loaded, loop
     *
     * @param  Event $e
     * @return void
     */
    public function onLoadModules(Event $e)
    {
        $moduleManager = $e->getTarget();
        $em        = $moduleManager->getSharedEventManager();

        if (!$em) {
            return;
        }

        // Shared instance for module manager
        $em->attach(new CallbackListener(
            'Zend\Mvc\Application',
            MvcEvent::EVENT_BOOTSTRAP,
            function ($e) use ($moduleManager) {
                $moduleClassName = get_class($moduleManager);

                $moduleClassNameArray = explode('\\', $moduleClassName);

                $moduleClassNameAlias = end($moduleClassNameArray);

                $application = $e->getApplication();
                $services    = $application->getServiceManager();

                if (!$services->has($moduleClassName)) {
                    $services->setAlias($moduleClassName, $moduleClassNameAlias);
                }
            },
            1000
        ));

        if (0 === count($this->modules)) {
            return;
        }

        // Attach to the bootstrap event if there are modules we need to process
        $events->attach(new CallbackListener(array($this, 'onBootstrap'), MvcEvent::EVENT_BOOTSTRAP, 'Zend\Mvc\Application', 1000));
    }

    /**
     * Bootstrap listener
     *
     * This is ran during the MVC bootstrap event because it requires access to
     * the DI container.
     *
     * @TODO: Check the application / locator / etc a bit better to make sure
     * the env looks how we're expecting it to?
     * @param Event $e
     * @return void
     */
    public function onBootstrap(Event $e)
    {
        $application = $e->getApplication();
        $services    = $application->getServiceManager();

        foreach ($this->modules as $module) {
            $moduleClassName = get_class($module);
            if (!$services->has($moduleClassName)) {
                $services->setService($moduleClassName, $module);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManager $em)
    {
        $this->callbacks[] = $em->attach(new CallbackListener(array($this, 'onLoadModule'), ModuleEvent::EVENT_LOAD_MODULE));
        $this->callbacks[] = $em->attach(new CallbackListener(array($this, 'onLoadModules'), ModuleEvent::EVENT_LOAD_MODULES, null, -1000));
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function detach(EventManager $em)
    {
        foreach ($this->callbacks as $index => $callback) {
            if ($em->detach($callback)) {
                unset($this->callbacks[$index]);
            }
        }
    }
}
