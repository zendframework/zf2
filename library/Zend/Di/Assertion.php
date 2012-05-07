<?php

namespace Zend\Di;
use Zend\Di\Definition\Definition;

/**
 * Assertion interface.
 */
interface Assertion
{
    /**
     * assert function.
     * 
     * @access public
     * @param mixed $class
     * @param mixed Definition $definition. (default: null)
     * @return void
     */
    public function assert($class, Definition $definition = null);

    /**
     * __toString function.
     * 
     * @access public
     * @return void
     */
    public function __toString();
}
