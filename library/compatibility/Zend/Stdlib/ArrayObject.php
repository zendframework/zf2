<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib;

use ArrayObject as PhpArrayObject;

/**
 * This is a compatibility implementation of ArrayObject for PHP <= 5.3.3
 * This is automatically loaded instead of the core implementation by the 
 * compatibility boostrap file
 */
class ArrayObject extends PhpArrayObject
{
    /**
     * Constructor
     *
     * @param  array       $input
     * @param  int         $flags
     * @param  string      $iteratorClass
     * @return ArrayObject
     */
    public function __construct($input = array(), $flags = self::STD_PROP_LIST, $iteratorClass = 'ArrayIterator')
    {
        parent::__construct($input, $flags, $iteratorClass);
    }
}
