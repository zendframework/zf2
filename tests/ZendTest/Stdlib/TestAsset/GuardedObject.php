<?php

namespace ZendTest\Stdlib\TestAsset;

use Zend\Stdlib\Guard;

class GuardedObject
{

    use Guard\ArrayOrTraversableGuardTrait;
    use Guard\NullGuardTrait;
    use Guard\EmptyGuardTrait;

    public function setArrayOrTraversable($data)
    {
        return $this->guardForArrayOrTraversable($data);
    }

    public function setNotNull($data)
    {
        return $this->guardAgainstNull($data);
    }

    public function setNotEmpty($data)
    {
        return $this->guardAgainstEmpty($data);
    }

}