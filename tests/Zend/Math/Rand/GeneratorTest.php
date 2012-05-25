<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Math
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Math\Rand;

use Zend\Math\Rand\StaticGenerator as Rng;

/**
 * @category   Zend
 * @package    Zend_Math
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Math
 */
class MathTest extends \PHPUnit_Framework_TestCase
{

    public function testGetBytes()
    {
        for ($length = 1; $length < 64; $length++) {
            $rand = Rng::getBytes($length);
            $this->assertTrue(!empty($rand));
            $this->assertEquals(strlen($rand), $length);
        }
    }

    public function testGetInteger()
    {
        for ($i = 0; $i < 100; $i++) {
            $min = mt_rand(1, 10000);
            $max = $min + mt_rand(1, 10000);
            $rand = Rng::getInteger($min, $max);
            $this->assertTrue(($rand >= $min) && ($rand <= $max));
        }
    }

    public function testGenerateIntLarge()
    {
        $bits = 30;
        $expected = 50529027;
        if (PHP_INT_MAX > 4000000000) {
            $bits = 56;
            $expected = 1693273676973062;
        }
        $n = Rng::getInteger(0, pow(2, $bits));
        $this->assertEquals($expected, $n);
    }

    public function testGetFloat()
    {
        for ($i = 0; $i < 100; $i++) {
            $rand = Rng::getFloat();
            $this->assertTrue(($rand >= 0) && ($rand <= 1));
        }
    }

    public function testGetString()
    {
        for ($length = 1; $length < 129; $length++) {
            $rand = Rng::getString($length, '0123456789abcdef');
            $this->assertEquals(strlen($rand), $length);
            $this->assertTrue(preg_match('#^[0-9a-f]+$#', $rand) === 1);
        }
    }

    public function testGetStringBase64()
    {
        for ($length = 1; $length < 65; $length++) {
            $rand = Rng::getString($length);
            $this->assertEquals(strlen($rand), $length);
            $this->assertTrue(preg_match('#^[0-9a-zA-Z+/]+$#', $rand) === 1);
        }
    }

}
