<?php

namespace ZendTest\Di\TestAsset\SetterInjection;

/**
 * Test mock used to verify that injections are not duplicated because of class hierarchies
 */
class DuplicateInjectionParent
{
    /**
     * @var int
     */
    protected $injectionsCount = 0;

    /**
     * @param A $a
     */
    public function setA(A $a) {
        $this->injectionsCount += 1;
    }

    /**
     * @return int
     */
    public function getInjectionsCount()
    {
        return $this->injectionsCount;
    }
}
