<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Guard;

use PHPUnit_Framework_TestCase as TestCase;
use ZendTest\Stdlib\TestAsset\GuardedObject;

/**
 * @covers   Zend\Stdlib\Guard\EmptyGuardTrait
 */
class EmptyGuardTraitTest extends TestCase
{
    public function testGuardAgainstEmptyThrowsException()
    {
        $object = new GuardedObject;
        $this->setExpectedException(
            'Zend\Stdlib\Exception\InvalidArgumentException',
            'Argument cannot be empty'
        );
        $object->setNotEmpty('');
    }

    public function testGuardAgainstEmptyAllowsNonEmptyString()
    {
        $object = new GuardedObject;
        $this->assertNull($object->setNotEmpty('foo'));
    }
}
