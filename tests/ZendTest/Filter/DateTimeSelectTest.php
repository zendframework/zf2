<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Filter;

use Zend\Filter\DateTimeSelect as DateTimeSelectFilter;

class DateTimeSelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideFilter
     * @param $options
     * @param $input
     * @param $expected
     */
    public function testFilter($options, $input, $expected)
    {
        $sut = new DateTimeSelectFilter();
        $sut->setOptions($options);
        $this->assertEquals($expected, $sut->filter($input));
    }

    public function provideFilter()
    {
        return [
            [
                [],
                ['year' => '2014', 'month' => '10', 'day' => '26', 'hour' => '12', 'minute' => '35'],
                '2014-10-26 12:35:00'
            ],
            [
                ['nullOnEmpty' => true],
                ['year' => null, 'month' => '10', 'day' => '26', 'hour' => '12', 'minute' => '35'],
                null
            ],
            [
                ['null_on_empty' => true],
                ['year' => null, 'month' => '10', 'day' => '26', 'hour' => '12', 'minute' => '35'],
                null
            ],
            [
                ['nullOnAllEmpty' => true],
                ['year' => null, 'month' => null, 'day' => null, 'hour' => null, 'minute' => null],
                null
            ],
            [
                ['null_on_all_empty' => true],
                ['year' => null, 'month' => null, 'day' => null, 'hour' => null, 'minute' => null],
                null
            ],
        ];
    }

    /**
     * @expectedException \Zend\Filter\Exception\RuntimeException
     */
    public function testInvalidInput()
    {
        $sut = new DateTimeSelectFilter();
        $sut->filter(['year' => '2120', 'month' => '10', 'day' => '26', 'hour' => '12']);
    }
}
