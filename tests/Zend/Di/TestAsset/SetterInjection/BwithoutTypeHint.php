<?php

namespace ZendTest\Di\TestAsset\SetterInjection;

class BwithoutTypeHint
{
    public $a = null;
    public function setA($a)
    {
        $this->a = $a;
    }
}
