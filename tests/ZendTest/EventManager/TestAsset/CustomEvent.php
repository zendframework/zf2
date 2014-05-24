<?php

namespace ZendTest\EventManager\TestAsset;

use Zend\EventManager\Event;

class CustomEvent extends Event
{
    public function __clone()
    {
        $this->setParam('cloned', true);
    }
}