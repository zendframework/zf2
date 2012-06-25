<?php

namespace ZendTest\Stdlib\TestAsset;

use Zend\Stdlib\Options;

/**
 * Dummy TestOptions used to test Stdlib\Options
 */
class TestOptions extends Options
{
    protected $foo;

    protected $fooBar;

    protected $fooBarBaz;

    protected $fooBar2Baz;


    public function setFoo($foo)
    {
        $this->foo = $foo;
    }
    
    public function getFoo()
    {
        return $this->foo;
    }

    public function setFooBar($fooBar)
    {
        $this->fooBar = $fooBar;
    }

    public function getFooBar()
    {
        return $this->fooBar;
    }

    public function setFooBarBaz($fooBarBaz)
    {
        $this->fooBarBaz = $fooBarBaz;
    }

    public function getFooBarBaz()
    {
        return $this->fooBarBaz;
    }
}
