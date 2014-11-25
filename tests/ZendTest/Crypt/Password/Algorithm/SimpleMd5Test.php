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
use Zend\Crypt\Password\Algorithm\SimpleMd5;

/**
 * @covers Zend\Crypt\Password\Algorithm\SimpleMd5
 */
class SimpleMd5Test extends TestCase
{
    public function testSupports()
    {
        $handler = new SimpleMd5();
        $this->assertTrue($handler->supports('$simple-md5$3858f62230ac3c915f300c664312c63f'));
        $this->assertFalse($handler->supports('3858f62230ac3c915f300c664312c63f'));
    }

    public function testHash()
    {
        $handler = new SimpleMd5();
        $this->assertEquals('$simple-md5$3858f62230ac3c915f300c664312c63f', $handler->hash('foobar'));
    }

    public function testCompare()
    {
        $handler = new SimpleMd5();
        $this->assertTrue($handler->compare('foobar', '$simple-md5$3858f62230ac3c915f300c664312c63f'));
        $this->assertFalse($handler->compare('foobaz', '$simple-md5$3858f62230ac3c915f300c664312c63f'));
    }

    public function testShouldRehash()
    {
        $handler = new SimpleMd5();
        $this->assertFalse($handler->shouldRehash(null));
    }
}
