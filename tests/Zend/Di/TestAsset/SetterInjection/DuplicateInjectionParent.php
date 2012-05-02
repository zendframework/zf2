<?php

namespace ZendTest\Di\TestAsset\SetterInjection;

/**
 * Test mock used to verify that injections are not duplicated because of class hierarchies
 */
class DuplicateInjectionParent
{
    /**
     * @var array
     */
    public $injections = array();

    /**
     * @param string $a
     * @return void
     */
    public function setA($a) {
        $this->injections[] = $a;
    }

    /**
     * @return int
     */
    public function getInjectionsCount()
    {
        return count($this->injections);
    }
}
