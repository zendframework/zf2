<?php

namespace ZendTest\Di\TestAsset\SetterInjection;

class E
{
    public $a = null;
    
    public function setA($a)
    {
        $this->a = $a;
    }
}