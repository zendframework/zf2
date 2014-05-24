<?php

namespace Zend\EventManager\Provider;

interface ProviderInterface
{
    /**
     * @param $eventName
     * @param $target
     * @param array $parameters
     * @return \Zend\EventManager\EventInterface
     */
    public function get($eventName, $target = null, $parameters = array());
}