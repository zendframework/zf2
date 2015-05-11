<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Filter;

use Zend\Filter\MonthSelect as MonthSelectFilter;

class MonthSelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideFilter
     * @param $options
     * @param $input
     * @param $expected
     */
    public function testFilter($options, $input, $expected)
    {
        $sut = new MonthSelectFilter();
        $sut->setOptions($options);
        $this->assertEquals($expected, $sut->filter($input));
    }

    public function provideFilter()
    {
        return [
            [[], ['year' => '2014', 'month' => '10'], '2014-10'],
            [['nullOnEmpty' => true], ['year' => null, 'month' => '10'], null],
            [['null_on_empty' => true], ['year' => null, 'month' => '10'], null],
            [['nullOnAllEmpty' => true], ['year' => null, 'month' => null], null],
            [['null_on_all_empty' => true], ['year' => null, 'month' => null], null],
        ];
    }

    /**
     * @expectedException \Zend\Filter\Exception\RuntimeException
     */
    public function testInvalidInput()
    {
        $sut = new MonthSelectFilter();
        $sut->filter(['year' => '2120']);
    }
}
