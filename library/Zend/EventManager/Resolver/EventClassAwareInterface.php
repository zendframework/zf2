<?php

namespace Zend\EventManager\Resolver;

/**
 * Provides compatibility layer with EventManager::setEventClass($class)
 */
interface EventClassAwareInterface
{
    /**
     * @param string $class    The class name
     * @return self
     */
    public function setEventClass($class);
}