<?php

namespace Zend\Di;

interface DependencyInjection extends Locator
{
    /**
     * Retrieve a new instance of a class
     *
     * Forces retrieval of a discrete instance of the given class, using the
     * constructor parameters provided.
     * 
     * @param  mixed $name Class name or service alias
     * @param  array $params Parameters to pass to the constructor
     * @param  Assertion|null $assertion 
     * @param  bool $isShared
     * @return object|null
     */
    public function newInstance($name, array $params = array(), Assertion $assertion = null, $isShared = true);
}
