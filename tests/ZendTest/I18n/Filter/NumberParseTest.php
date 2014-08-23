<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\I18n\Filter;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\I18n\Filter\NumberParse as NumberParseFilter;
use NumberFormatter;

class NumberParseTest extends TestCase
{
    public function setUp()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }
    }

    public function testConstructWithOptions()
    {
        $filter = new NumberParseFilter(array(
            'locale' => 'en_US',
            'style'  => NumberFormatter::DECIMAL,
            'type' => NumberFormatter::TYPE_DOUBLE,
            'decimals' => 2
        ));

        $this->assertEquals('en_US', $filter->getLocale());
        $this->assertEquals(NumberFormatter::DECIMAL, $filter->getStyle());
        $this->assertEquals(NumberFormatter::TYPE_DOUBLE, $filter->getType());
        $this->assertEquals(2, $filter->getDecimals());
    }

    public function testConstructWithParameters()
    {
        $filter = new NumberParseFilter('en_US', NumberFormatter::DECIMAL, NumberFormatter::TYPE_DOUBLE, 2);

        $this->assertEquals('en_US', $filter->getLocale());
        $this->assertEquals(NumberFormatter::DECIMAL, $filter->getStyle());
        $this->assertEquals(NumberFormatter::TYPE_DOUBLE, $filter->getType());
        $this->assertEquals(2, $filter->getDecimals());
    }

    /**
     * @param $locale
     * @param $style
     * @param $type
     * @param $decimals
     * @param $value
     * @param $expected
     * @dataProvider formattedToNumberProvider
     */
    public function testFormattedToNumber($locale, $style, $type, $decimals, $value, $expected)
    {
        $filter = new NumberParseFilter($locale, $style, $type, $decimals);
        $this->assertSame($expected, $filter->filter($value));
    }

    public static function formattedToNumberProvider()
    {
        return array(
            array(
                'en_US',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                3,
                '1,234,567.891',
                1234567.891,
            ),
            array(
                'de_DE',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                3,
                '1.234.567,891',
                1234567.891,
            ),
            array(
                'ru_RU',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                3,
                '1 234 567,891',
                1234567.891,
            ),
        );
    }
}
