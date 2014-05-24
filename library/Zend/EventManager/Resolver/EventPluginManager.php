<?php

namespace Zend\EventManager\Resolver;

use Zend\EventManager\EventInterface;
use Zend\EventManager\Exception\RuntimeException;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Manage events resolution as services.
 * If a triggered event has other that an EventInterface as first parameter, and this manager is set
 * as event resolver for EventManager, then the first parameter will be used as service index key.
 */
class EventPluginManager extends AbstractPluginManager implements ResolverInterface
{
    /**
     * Do not share events instances by default
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * Validate the plugin
     *
     * @param  mixed $plugin
     * @return void
     * @throws RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if (! $plugin instanceof EventInterface) {
            throw new RuntimeException(sprintf(
                'Expecting an instance of Zend\EventManager\EventInterface, %s given',
                is_object($plugin) ? get_class($plugin) : gettype($plugin)
            ));
        }
    }
}