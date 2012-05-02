<?php

namespace ZendTest\Di\TestAsset\SetterInjection;

/**
 * Test mock used to verify that injections are not duplicated because of class hierarchies
 */
class DuplicateInjection extends DuplicateInjectionParent
{
    /**
     * @param string $a
     * @return void
     */
    public function setA($a) {
        parent::setA($a);
    }
}
