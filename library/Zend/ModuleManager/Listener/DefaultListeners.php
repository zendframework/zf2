<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ModuleManager\Listener;

use Zend\ModuleManager\Listener\ListenerOptions;

use Zend\Framework\EventManager\EventManager;
use Zend\Framework\EventManager\ListenerAggregateInterface;
use Zend\ModuleManager\ModuleEvent;
use Zend\Stdlib\CallbackHandler;

/**
 * Default listener aggregate
 */
class DefaultListeners implements ListenerAggregateInterface
{
    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * @var ConfigMergerInterface
     */
    protected $configListener;

    public function __construct(ListenerOptions $options)
    {
        $this->options = $options;
    }

    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Attach one or more listeners
     *
     * @param  EventManager $em
     * @return DefaultListenerAggregate
     */
    public function attach(EventManager $em)
    {
        $options = $this->options;

        // High priority, we assume module autoloading (for FooNamespace\Module classes) should be available before anything else
        $this->listeners[] = $em->attach(new ModuleLoaderListener($options, ModuleEvent::EVENT_LOAD_MODULE, null, 10000));

        $this->listeners[] = $em->attach(new ModuleResolverListener(ModuleEvent::EVENT_LOAD_MODULE_RESOLVE));

        // High priority, because most other loadModule listeners will assume the module's classes are available via autoloading
        $this->listeners[] = $em->attach(new AutoloaderListener($options, ModuleEvent::EVENT_LOAD_MODULE, null, 9000));

        if ($options->getCheckDependencies()) {
            $this->listeners[] = $em->attach(new ModuleDependencyCheckerListener(ModuleEvent::EVENT_LOAD_MODULE, null, 8000));
        }

        $this->listeners[] = $em->attach(new InitTrigger($options, ModuleEvent::EVENT_LOAD_MODULE));
        $this->listeners[] = $em->attach(new OnBootstrapListener($options, ModuleEvent::EVENT_LOAD_MODULE));
        $this->listeners[] = $em->attach(new LocatorRegistrationListener($options));
        $this->listeners[] = $em->attach($this->getConfigListener());
        return $this;
    }

    /**
     * Detach all previously attached listeners
     *
     * @param  EventManager $em
     * @return void
     */
    public function detach(EventManager $em)
    {
        foreach ($this->listeners as $key => $listener) {
            $detached = false;
            if ($listener === $this) {
                continue;
            }
            if ($listener instanceof ListenerAggregateInterface) {
                $detached = $listener->detach($events);
            } elseif ($listener instanceof CallbackHandler) {
                $detached = $events->detach($listener);
            }

            if ($detached) {
                unset($this->listeners[$key]);
            }
        }
    }

    /**
     * Get the config merger.
     *
     * @return ConfigMergerInterface
     */
    public function getConfigListener()
    {
        if (!$this->configListener instanceof ConfigMergerInterface) {
            $this->setConfigListener(new ConfigListener($this->getOptions()));
        }
        return $this->configListener;
    }

    /**
     * Set the config merger to use.
     *
     * @param  ConfigMergerInterface $configListener
     * @return DefaultListenerAggregate
     */
    public function setConfigListener(ConfigMergerInterface $configListener)
    {
        $this->configListener = $configListener;
        return $this;
    }
}
