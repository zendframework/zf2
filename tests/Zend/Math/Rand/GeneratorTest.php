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
use ZendTest\Math\Rand\TestAsset\MockGenerator as MockRng;

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
    protected $mockBytes = array(
        'af08a55994f2c60deeada9855e1007300c9812fc0f6c92314a75cbbfa1390dfa',
        '03606d0d428f8b02b7e82161c1ca31d000a9b33d49462ba9d649886b41d1ddf9',
        '49b56d8573edd2a5fb21cc2af66db800a7b2ebcfd9399783cfcd70a5367a86e5',
        '58b5588124f1bc2f114072000537026ff778fe5a2c5d2594abe2e5d54c2a10e7',
        'b86004e3eb836ca477c4bb7ef10aeacce4d22de8583f9ac39781c3ca1fa56080',
        '37f60d65da7fdb520d7f9c0c1972dc85512ba52441ef2f605689ab20755dd9e4',
        '7bad2e0eb88e1fcb0af79aa0c7bc13a595d390063ff4f9e4f60ff40bf91ae9da',
        'b68eb6a0e21e2ea2be66429ddc1c65f2b93f3312522063dfec7eb6514dd9d2b2',
        '614cc7d80b0b4a1369835b6a8c3d78f0f40e575ce24696c23f1d4d689098f2c5',
        'a248c6edfd34e35f0beb26138188f71788ab12aebcf83e7051674acf3ab66c0f',
        '739febb656ef727eedc1c2cc549314e35715286a9f40177ae994a91da0e15f49',
        'c0ddca8c76e1eee2aa330308ecde14085f51aced9ac71a27f8ba4ffefab820e6',
        'f53fbcce6dcc6ad2ee71e817922ee45bbeaee5c068e42d8e51b6a01d061be64a',
        'e25c1a43e2055793b7d4e7e5e543bcaf4b84226c9d4759442352526401cff473',
        '5eeefdea0158e07d2dfcbee379f5c3c6761828d5bdd8a73de0cc025299e78cfe',
        'ec26791e69ddde32fb55d21241b9da5f816d4ce7678cb2a571575a3422506981',
    );

    public function testMockGetBytes()
    {
        $rng = new MockRng();
        foreach ($this->mockBytes as $bytes) {
            $this->assertEquals($bytes, bin2hex($rng->getBytes(32)));
        }
    }

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
        for ($i = 0; $i < 50; $i++) {
            $min = mt_rand(1, 10000);
            $max = $min + mt_rand(1, 10000);
            $rand = Rng::getInteger($min, $max);
            $this->assertTrue(($rand >= $min) && ($rand <= $max));
        }
    }

    public function testGetFloat()
    {
        for ($i = 0; $i < 50; $i++) {
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
