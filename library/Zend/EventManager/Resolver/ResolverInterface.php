<?php

namespace Zend\EventManager\Resolver;

interface ResolverInterface
{
    /**
     * @param $eventName
     * @return \Zend\EventManager\EventInterface
     */
    public function get($eventName);
}