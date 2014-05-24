<?php

namespace Zend\EventManager\Provider;

interface ProviderInterface
{
    /**
     * @param $eventName
     * @param $context
     * @param array $parameters
     * @return \Zend\EventManager\EventInterface
     */
    public function get($eventName, $context = null, $parameters = []);
}