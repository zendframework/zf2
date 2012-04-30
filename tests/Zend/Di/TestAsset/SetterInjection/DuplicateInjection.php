<?php

namespace ZendTest\Di\TestAsset\SetterInjection;

/**
 * Test mock used to verify that injections are not duplicated because of class hierarchies
 */
class DuplicateInjection extends DuplicateInjectionParent
{
    /**
     * @param \ZendTest\Di\TestAsset\SetterInjection\A $a
     * @return void
     */
    public function setA(A $a) {
        parent::setA($a);
    }
}
