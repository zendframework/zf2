<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\I18n\Filter;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\I18n\Filter\NumberParse as NumberParseFilter;
use NumberFormatter;

class NumberParseTest extends TestCase
{
    public function testConstructWithOptions()
    {
        $filter = new NumberParseFilter(array(
            'locale' => 'en_US',
            'style'  => NumberFormatter::DECIMAL
        ));

        $this->assertEquals('en_US', $filter->getLocale());
        $this->assertEquals(NumberFormatter::DECIMAL, $filter->getStyle());
    }

    public function testConstructWithParameters()
    {
        $filter = new NumberParseFilter('en_US', NumberFormatter::DECIMAL);

        $this->assertEquals('en_US', $filter->getLocale());
        $this->assertEquals(NumberFormatter::DECIMAL, $filter->getStyle());
    }

    /**
     * @param $locale
     * @param $style
     * @param $type
     * @param $value
     * @param $expected
     * @dataProvider formattedToNumberProvider
     */
    public function testFormattedToNumber($locale, $style, $type, $value, $expected)
    {
        $filter = new NumberParseFilter($locale, $style, $type);
        $this->assertSame($expected, $filter->filter($value));
    }

    public static function formattedToNumberProvider()
    {
        return array(
            array(
                'en_US',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                '1,234,567.891',
                1234567.891,
            ),
            array(
                'de_DE',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                '1.234.567,891',
                1234567.891,
            ),
            array(
                'ru_RU',
                NumberFormatter::DEFAULT_STYLE,
                NumberFormatter::TYPE_DOUBLE,
                '1 234 567,891',
                1234567.891,
            ),
        );
    }


    public function testParseDoNotChangeLocale()
    {
        $currentLocale = setlocale(LC_NUMERIC, 0); // get original locale

        setlocale(LC_NUMERIC, 'fr_FR');
        $filter = new NumberParseFilter('en_US', NumberFormatter::TYPE_DEFAULT);
        $filter->filter('10.01');
        $this->assertEquals('fr_FR', setlocale(LC_NUMERIC, 0));

        setlocale(LC_NUMERIC, $currentLocale); // reset locale
    }
}
