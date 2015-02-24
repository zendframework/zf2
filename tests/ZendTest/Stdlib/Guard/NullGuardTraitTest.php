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
 * @covers   Zend\Stdlib\Guard\NullGuardTrait
 */
class NullGuardTraitTest extends TestCase
{
    public function testGuardAgainstNullThrowsException()
    {
        $object = new GuardedObject;
        $this->setExpectedException(
            'Zend\Stdlib\Exception\InvalidArgumentException',
            'Argument cannot be null'
        );
        $object->setNotNull(null);
    }

    public function testGuardAgainstNullAllowsNonNull()
    {
        $object = new GuardedObject;
        $this->assertNull($object->setNotNull('foo'));
    }
}
