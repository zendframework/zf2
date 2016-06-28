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
use Zend\Crypt\Password\Algorithm\Bcrypt;
use Zend\Crypt\Password\Bcrypt as BcryptHasher;

/**
 * @covers Zend\Crypt\Password\Algorithm\Bcrypt
 */
class BcryptTest extends TestCase
{
    public function testSupports()
    {
        $handler = new Bcrypt();
        $this->assertTrue($handler->supports('$2y$04$MDAwMDAwMDAwMDAwMDAwM.ZPgzzqxELAsWis/z0SGoAUQ8M2HIs1.'));
        $this->assertFalse($handler->supports('MDAwMDAwMDAwMDAwMDAwM.ZPgzzqxELAsWis/z0SGoAUQ8M2HIs1.'));
    }

    public function testHash()
    {
        $handler = new Bcrypt();
        $handler->getBackend()->setCost(4)->setSalt('0000000000000000');
        $this->assertEquals('$2y$04$MDAwMDAwMDAwMDAwMDAwM.ZPgzzqxELAsWis/z0SGoAUQ8M2HIs1.', $handler->hash('foobar'));
    }

    public function testCompare()
    {
        $handler = new Bcrypt();
        $handler->getBackend()->setCost(4);
        $this->assertTrue($handler->compare('foobar', '$2y$04$MDAwMDAwMDAwMDAwMDAwM.ZPgzzqxELAsWis/z0SGoAUQ8M2HIs1.'));
        $this->assertFalse($handler->compare('foobaz', '$2y$04$MDAwMDAwMDAwMDAwMDAwM.ZPgzzqxELAsWis/z0SGoAUQ8M2HIs1.'));
    }

    public function testShouldRehash()
    {
        $handler = new Bcrypt();

        $handler->getBackend()->setCost(4);
        $this->assertFalse(
            $handler->shouldRehash('$2y$04$MDAwMDAwMDAwMDAwMDAwM.ZPgzzqxELAsWis/z0SGoAUQ8M2HIs1.'),
            'Equal cost must not trigger rehash'
        );

        $handler->getBackend()->setCost(6);
        $this->assertTrue(
            $handler->shouldRehash('$2y$04$MDAwMDAwMDAwMDAwMDAwM.ZPgzzqxELAsWis/z0SGoAUQ8M2HIs1.'),
            'Higher cost must trigger rehash'
        );

        $handler->getBackend()->setCost(6);
        $this->assertTrue(
            $handler->shouldRehash('foobar'),
            'Invalid hash must trigger rehash'
        );
    }

    public function testSetBackend()
    {
        $backend = new BcryptHasher();
        $handler = new Bcrypt();
        $handler->setBackend($backend);

        $this->assertSame($backend, $handler->getBackend());
    }
}
