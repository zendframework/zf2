<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Math
 */

namespace ZendTest\Math;

use Zend\Math\Math;

/**
 * @category   Zend
 * @package    Zend_Math
 * @subpackage UnitTests
 * @group      Zend_Math
 */
class MathTest extends \PHPUnit_Framework_TestCase
{
    public static function provideRandInt()
    {
        return array(
            array(2, 1, 10000, 100, 0.9, 1.1, false),
            array(2, 1, 10000, 100, 0.8, 1.2, true)
        );
    }

    public function testRandBytes()
    {
        for ($length = 1; $length < 4096; $length++) {
            $rand = Math::randBytes($length);
            $this->assertTrue($rand !== false);
            $this->assertEquals($length, strlen($rand));
        }
    }

    /**
     * A Monte Carlo test that generates $cycles numbers from 0 to $tot
     * and test if the numbers are above or below the line y=x with a
     * frequency range of [$min, $max]
     *
     * Note: this code is inspired by the random number generator test
     * included in the PHP-CryptLib project of Anthony Ferrara
     * @see https://github.com/ircmaxell/PHP-CryptLib
     *
     * @dataProvider provideRandInt
     */
    public function testRandInt($num, $valid, $cycles, $tot, $min, $max, $strong)
    {
        try {
            $test = Math::randBytes(1, $strong);
        } catch (\Zend\Math\Exception\RuntimeException $e) {
            $this->markTestSkipped($e->getMessage());
        }
        $i     = 0;
        $count = 0;
        do {
            $up   = 0;
            $down = 0;
            for ($i=0; $i<$cycles; $i++) {
                $x = Math::rand(0, $tot, $strong);
                $y = Math::rand(0, $tot, $strong);
                if ($x > $y) {
                    $up++;
                } elseif ($x < $y) {
                    $down++;
                }
            }
            $this->assertGreaterThan(0, $up);
            $this->assertGreaterThan(0, $down);
            $ratio = $up / $down;
            if ($ratio > $min && $ratio < $max) {
                $count++;
            }
            $i++;
        } while ($i < $num && $count < $valid);
        if ($count < $valid) {
            $this->fail('The random number generator failed the Monte Carlo test');
        }
    }
}
