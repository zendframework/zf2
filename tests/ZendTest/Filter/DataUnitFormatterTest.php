<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Filter;

use Zend\Filter\DataUnitFormatter as DataUnitFormatterFilter;

/**
 * @group      Zend_Filter
 */
class DataUnitFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param float $value
     * @param string $expected
     * @dataProvider decimalBytesTestProvider
     */
    public function testDecimalBytes($value, $expected)
    {
        $filter = new DataUnitFormatterFilter([
            'mode' => DataUnitFormatterFilter::MODE_DECIMAL,
            'unit' => 'B'
        ]);
        $this->assertEquals($expected, $filter->filter($value));
    }

    /**
     * @param float $value
     * @param string $expected
     * @dataProvider binaryBytesTestProvider
     */
    public function testBinaryBytes($value, $expected)
    {
        $filter = new DataUnitFormatterFilter([
            'mode' => DataUnitFormatterFilter::MODE_BINARY,
            'unit' => 'B'
        ]);
        $this->assertEquals($expected, $filter->filter($value));
    }

    public function testPrecision()
    {
        $filter = new DataUnitFormatterFilter([
            'unit' => 'B',
            'precision' => 3,
        ]);

        $this->assertEquals('1.500 kB', $filter->filter(1500));
    }

    public function testCustomPrefixes()
    {
        $filter = new DataUnitFormatterFilter([
            'unit' => 'B',
            'prefixes' => ['', 'kilos'],
        ]);

        $this->assertEquals('1.50 kilosB', $filter->filter(1500));
    }

    public function testSettingNoOptions()
    {
        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException');
        $filter = new DataUnitFormatterFilter();
    }

    public function testSettingNoUnit()
    {
        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException');
        $filter = new DataUnitFormatterFilter([]);
    }

    public function testSettingFalseMode()
    {
        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException');
        $filter = new DataUnitFormatterFilter([
            'unit' => 'B',
            'mode' => 'invalid',
        ]);
    }

    public static function decimalBytesTestProvider()
    {
        return [
            [0, '0 B'],
            [1, '1.00 B'],
            [pow(1000, 1), '1.00 kB'],
            [pow(1500, 1), '1.50 kB'],
            [pow(1000, 2), '1.00 MB'],
            [pow(1000, 3), '1.00 GB'],
            [pow(1000, 4), '1.00 TB'],
            [pow(1000, 5), '1.00 PB'],
            [pow(1000, 6), '1.00 EB'],
            [pow(1000, 7), '1.00 ZB'],
            [pow(1000, 8), '1.00 YB'],
            [pow(1000, 9), (pow(1000, 9) . ' B')],
        ];
    }

    public static function binaryBytesTestProvider()
    {
        return [
            [0, '0 B'],
            [1, '1.00 B'],
            [pow(1024, 1), '1.00 KiB'],
            [pow(1536, 1), '1.50 KiB'],
            [pow(1024, 2), '1.00 MiB'],
            [pow(1024, 3), '1.00 GiB'],
            [pow(1024, 4), '1.00 TiB'],
            [pow(1024, 5), '1.00 PiB'],
            [pow(1024, 6), '1.00 EiB'],
            [pow(1024, 7), '1.00 ZiB'],
            [pow(1024, 8), '1.00 YiB'],
            [pow(1024, 9), (pow(1024, 9) . ' B')],
        ];
    }
}
