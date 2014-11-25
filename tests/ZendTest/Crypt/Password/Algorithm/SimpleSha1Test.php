<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\CryptTest\Password;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Crypt\Password\Algorithm\SimpleSha1;

/**
 * @covers Zend\Crypt\Password\Algorithm\SimpleSha1
 */
class SimpleSha1Test extends TestCase
{
    public function testSupports()
    {
        $handler = new SimpleSha1();
        $this->assertTrue($handler->supports('$simple-sha1$8843d7f92416211de9ebb963ff4ce28125932878'));
        $this->assertFalse($handler->supports('8843d7f92416211de9ebb963ff4ce28125932878'));
    }

    public function testHash()
    {
        $handler = new SimpleSha1();
        $this->assertEquals('$simple-sha1$8843d7f92416211de9ebb963ff4ce28125932878', $handler->hash('foobar'));
    }

    public function testCompare()
    {
        $handler = new SimpleSha1();
        $this->assertTrue($handler->compare('foobar', '$simple-sha1$8843d7f92416211de9ebb963ff4ce28125932878'));
        $this->assertFalse($handler->compare('foobaz', '$simple-sha1$8843d7f92416211de9ebb963ff4ce28125932878'));
    }

    public function testShouldRehash()
    {
        $handler = new SimpleSha1();
        $this->assertFalse($handler->shouldRehash(null));
    }
}
