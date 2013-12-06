<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ModuleManager\Listener;

use Zend\Framework\EventManager\EventManager;
use Zend\Framework\EventManager\Listener as EventListener;
use Zend\Framework\EventManager\ListenerAggregateInterface;
use Zend\Framework\EventManager\CallbackListener;

use Zend\Loader\ModuleAutoloader;
use Zend\ModuleManager\ModuleEvent;

use Traversable;

/**
 * Module loader listener
 */
class ModuleLoaderListener extends EventListener implements ListenerAggregateInterface
{
    /**
     * @var array
     */
    protected $moduleLoader;

    /**
     * @var bool
     */
    protected $generateCache;

    /**
     * @var array
     */
    protected $callbacks = [];

    /**
     * @var array|ListenerOptions
     */
    protected $options = [];

    /**
     * Constructor.
     *
     * Creates an instance of the ModuleAutoloader and injects the module paths
     * into it.
     *
     * @param  ListenerOptions $options
     */
    public function __construct(ListenerOptions $options = null, $event = null, $target = null, $priority = null)
    {
        parent::__construct($event, $target, $priority);

        $this->options = $options;

        $this->generateCache = $this->options->getModuleMapCacheEnabled();
        $this->moduleLoader  = new ModuleAutoloader($this->options->getModulePaths());

        if ($this->hasCachedClassMap()) {
            $this->generateCache = false;
            $this->moduleLoader->setModuleClassMap($this->getCachedConfig());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManager $em)
    {
        $this->callbacks[] = $em->attach(
            new CallbackListener(array($this->moduleLoader, 'register'), ModuleEvent::EVENT_LOAD_MODULES, null, 100000)
        );

        if ($this->generateCache) {
            $this->callbacks[] = $em->attach(
                new CallbackListener(array($this, 'onLoadModulesPost'), ModuleEvent::EVENT_LOAD_MODULES_POST)
            );
        }
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

    /**
     * @return bool
     */
    protected function hasCachedClassMap()
    {
        if (
            $this->options->getModuleMapCacheEnabled()
            && file_exists($this->options->getModuleMapCacheFile())
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    protected function getCachedConfig()
    {
        return include $this->options->getModuleMapCacheFile();
    }

    /**
     * loadModulesPost
     *
     * Unregisters the ModuleLoader and generates the module class map cache.
     *
     * @param  ModuleEvent $event
     */
    public function onLoadModulesPost(ModuleEvent $event)
    {
        $this->moduleLoader->unregister();
        $this->writeArrayToFile(
            $this->options->getModuleMapCacheFile(),
            $this->moduleLoader->getModuleClassMap()
        );
    }
}
