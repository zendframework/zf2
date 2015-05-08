<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Math\BigInteger\Adapter;

use Zend\Math\BigInteger\Adapter\AdapterInterface;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AdapterInterface
     */
    protected $adapter = null;

    /**
     * @param string $operand
     * @param string $expected
     * @dataProvider validInitProvider
     */
    public function testInit($operand, $expected)
    {
        $this->assertEquals($expected, $this->adapter->init($operand));
    }

    /**
     * @param string $operand
     * @dataProvider invalidInitProvider
     */
    public function testInitReturnsFalse($operand)
    {
        $this->assertFalse($this->adapter->init($operand));
    }

    /**
     * @param string $operation
     * @param string $op1
     * @param string $op2
     * @param string $expected
     * @dataProvider basicCalcProvider
     */
    public function testBasicCalc($operation, $op1, $op2, $expected)
    {
        $result = '';
        switch ($operation) {
            case 'add':
                $result = $this->adapter->add($op1, $op2);
                break;
            case 'sub':
                $result = $this->adapter->sub($op1, $op2);
                break;
            case 'mul':
                $result = $this->adapter->mul($op1, $op2);
                break;
            case 'div':
                $result = $this->adapter->div($op1, $op2);
                break;
            case 'pow':
                $result = $this->adapter->pow($op1, $op2);
                break;
            case 'mod':
                $result = $this->adapter->mod($op1, $op2);
                break;
        }

        $this->assertEquals($expected, $result, "Operation [{$op1} {$operation} {$op2}] has failed");
    }

    /**
     * @param string $op
     * @param string $expected
     * @dataProvider sqrtProvider
     */
    public function testSqrt($op, $expected)
    {
        $this->assertEquals($expected, $this->adapter->sqrt($op));
    }

    /**
     * @param string $op1
     * @param string $op2
     * @param string $mod
     * @param string $expected
     * @dataProvider powmodProvider
     */
    public function testPowMod($op1, $op2, $mod, $expected)
    {
        $this->assertEquals($expected, $this->adapter->powmod($op1, $op2, $mod));
    }

    /**
     * @param string $op
     * @param string $expected
     * @dataProvider absProvider
     */
    public function testAbs($op, $expected)
    {
        $this->assertEquals($expected, $this->adapter->abs($op));
    }

    /**
     * @param string $op1
     * @param string $op2
     * @param string $expected
     * @dataProvider comparisonProvider
     */
    public function testComparison($op1, $op2, $expected)
    {
        $this->assertEquals($expected, $this->adapter->comp($op1, $op2));
    }

    /**
     * @param string $op
     * @param string $baseFrom
     * @param string $baseTo
     * @param string $expected
     * @dataProvider baseConversionProvider
     */
    public function testBaseConversion($op, $baseFrom, $baseTo, $expected)
    {
        $this->assertEquals($expected, $this->adapter->baseConvert($op, $baseFrom, $baseTo));
    }

    /**
     * @param string $op
     * @param string $bin
     * @param string $bin2c
     * @dataProvider binaryConversionProvider
     */
    public function testBinaryConversion($op, $bin, $bin2c)
    {
        $bin   = base64_decode($bin);
        $bin2c = base64_decode($bin2c);
        $opPos = ltrim($op, '-');

        $this->assertEquals($bin, $this->adapter->intToBin($op));
        $this->assertEquals($bin2c, $this->adapter->intToBin($op, true));
        $this->assertEquals($opPos, $this->adapter->binToInt($bin));
        $this->assertEquals($op, $this->adapter->binToInt($bin2c, true));
    }

    public function testDivisionByZeroRaisesException()
    {
        $this->setExpectedException('Zend\Math\BigInteger\Exception\DivisionByZeroException',
                                    'Division by zero');
        $this->adapter->div('12345', '0');
    }

    /**
     * Data provider for init() tests
     *
     * @return array
     */
    public function validInitProvider()
    {
        return [
            ['+0', '0'],
            ['-0', '0'],
            // decimal
            ['12345678', '12345678'],
            ['-12345678', '-12345678'],
            // octal
            ['0726746425', '123456789'],
            ['-0726746425', '-123456789'],
            // hex
            ['0X75BCD15', '123456789'],
            ['0x75bcd15', '123456789'],
            ['-0X75BCD15', '-123456789'],
            // scientific notation
            ['1.23456e5', '123456'],
            ['-1.23456789e8', '-123456789'],
        ];
    }

    /**
     * Data provider for init() tests
     * Expects iit() to return false on these values
     *
     * @return array
     */
    public function invalidInitProvider()
    {
        return [
            ['zzz'],
            ['1/2'],
            ['1 + 2'],
            ['0.2E12'],
            ['1.2E-12'],
        ];
    }

    /**
     * Basic calculation data provider
     * add, sub, mul, div, pow, mod
     *
     * @return array
     */
    public function basicCalcProvider()
    {
        return [
            // addition
            ['add', '0', '12345', '12345'],
            ['add', '12345', '0', '12345'],
            ['add', '2', '2', '4'],
            ['add', '-2', '2', '0'],
            ['add', '-2', '-2', '-4'],

            // subtraction
            ['sub', '2', '0', '2'],
            ['sub', '0', '2', '-2'],
            ['sub', '2', '1', '1'],
            ['sub', '2', '-2', '4'],

            // multiplication
            ['mul', '2', '2', '4'],
            ['mul', '2', '-2', '-4'],
            ['mul', '2', '0', '0'],
            ['mul', '-2', '-2', '4'],

            // division
            ['div', '4', '2', '2'],
            ['div', '3', '2', '1'],
            ['div', '1', '2', '0'],
            ['div', '-2', '-2', '1'],

            // pow
            ['pow', '2', '2', '4'],
            ['pow', '2', '0', '1'],
            ['pow', '2', '64', '18446744073709551616'],
            ['pow', '-2', '64', '18446744073709551616'],

            // modulus
            ['mod', '3', '2', '1'],
            ['mod', '2', '2', '0'],
            ['mod', '2', '18446744073709551616', '2'],
        ];
    }

    /**
     * Square root tests data provider
     *
     * @return array
     */
    public function sqrtProvider()
    {
        return [
            ['4', '2'],
            ['4294967296', '65536'],
            ['12345678901234567890', '3513641828'], // truncated to int
        ];
    }

    /**
     * Power modulus data provider
     *
     * @return array
     */
    public function powmodProvider()
    {
        return [
            ['2', '2', '3', '1'],
        ];
    }

    /**
     * abs() tests data provider
     *
     * @return array
     */
    public function absProvider()
    {
        return [
            ['0', '0'],
            ['2', '2'],
            ['-2', '2'],
        ];
    }

    /**
     * Comparison function data provider
     *
     * @return array
     */
    public function comparisonProvider()
    {
        return [
            ['1', '0', 1],
            ['1', '1', 0],
            ['0', '1', -1],
            ['12345678901234567890', '1234567890123456789', 1],
            ['12345678901234567890', '12345678901234567890', 0],
            ['1234567890123456789', '12345678901234567890', -1],
        ];
    }

    /**
     * Base conversion data provider
     *
     * @return array
     */
    public function baseConversionProvider()
    {
        return [
            ['1234567890', 10, 2,  '1001001100101100000001011010010'],
            ['1234567890', 10, 8,  '11145401322'],
            ['1234567890', 10, 16, '499602d2'],
            ['1234567890', 10, 36, 'kf12oi'],
            ['1234567890', 10, 62, '1ly7vk'],

            // reverse
            ['1001001100101100000001011010010', 2, 10, '1234567890'],
            ['11145401322', 8, 10,  '1234567890'],
            ['499602d2',    16, 10,  '1234567890'],
            ['kf12oi',      36, 10,  '1234567890'],
            ['1ly7vk',      62, 10,  '1234567890'],
        ];
    }

    /**
     * binToInt() intToBin() tests provider
     *
     * @return array
     */
    public function binaryConversionProvider()
    {
        return [
            [
                '0',
                'AA==',
                'AA==',
            ],
            [
                // integer
                '1551728981814736974712322577637155399157248019669154044797077953140576293785419' .
                '1758065122742369818899372781615264663143856159582568818888995127215884267541995' .
                '0341258706556549803580104870537681476726513255747040765857479291291572334510643' .
                '245094715007229621094194349783925984760375594985848253359305585439638443',

                // binary
                '3Pk6C4g5cuwOGZiaxaLOMQ4dN3F+jZVxu3Yjcxhm5h73Wi4niYsFf5iRwuJ6Y5w/KbYIFFgc07LKOYbSaDc' .
                'FV31FwuflLcgcehcYduXOp0sUSL/frxiCjv0lGfFOReOCZjSvGUnltTXMgppIO4p2Ij5dSQolfwW9/xby+yLFg6s=',

                // binary two's complement
                'ANz5OguIOXLsDhmYmsWizjEOHTdxfo2Vcbt2I3MYZuYe91ouJ4mLBX+YkcLiemOcPym2CBRYHNOyyjmG0mg3B' .
                'Vd9RcLn5S3IHHoXGHblzqdLFEi/368Ygo79JRnxTkXjgmY0rxlJ5bU1zIKaSDuKdiI+XUkKJX8Fvf8W8vsixYOr',
            ],
            [
                '-1551728981814736974712322577637155399157248019669154044797077953140576293785419' .
                '1758065122742369818899372781615264663143856159582568818888995127215884267541995' .
                '0341258706556549803580104870537681476726513255747040765857479291291572334510643' .
                '245094715007229621094194349783925984760375594985848253359305585439638443',

                // binary
                '3Pk6C4g5cuwOGZiaxaLOMQ4dN3F+jZVxu3Yjcxhm5h73Wi4niYsFf5iRwuJ6Y5w/KbYIFFgc07LKOYbSaDc' .
                'FV31FwuflLcgcehcYduXOp0sUSL/frxiCjv0lGfFOReOCZjSvGUnltTXMgppIO4p2Ij5dSQolfwW9/xby+yLFg6s=',

                // negative binary, two's complement
                '/yMGxfR3xo0T8eZnZTpdMc7x4siOgXJqjkSJ3IznmRnhCKXR2HZ0+oBnbj0dhZxjwNZJ9+un4yxNNcZ5LZfI+q' .
                'iCuj0YGtI344Xo54kaMVi067dAIFDnfXEC2uYOsbocfZnLUOa2GkrKM31lt8R1id3Borb12oD6QgDpDQTdOnxV',
            ],
        ];
    }
}
