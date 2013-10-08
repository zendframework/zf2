<?php

namespace ZendTest\Stdlib\TestAsset;

use Zend\Stdlib\Guard;

class GuardedObject
{

    use Guard\AllGuardsTrait;

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
