<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace ZendTest\Filter;

use Zend\I18n\Filter\Boolean as BooleanFilter;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class BooleanTest extends \PHPUnit_Framework_TestCase
{
    public static function nativeBooleanProvider()
    {
        return array(
            array(1, true),
            array(0, false),
            array(true, true),
            array(false, false),
            array('true', true),
            array('tRuE', true),
            array('false', false),
            array('FalsE', false),
            array('on', true),
            array('off', false),
            array('yes', true),
            array('no', false)
        );
    }

    public static function localizedBooleanProvider()
    {
        return array(
            array('oui', true, true),
            array('non', false, false),
            array('はい', true, true),
            array('いいえ', false, false),
        );
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @param $value
     * @param $result
     * @return void
     * @dataProvider nativeBooleanProvider
     */
    public function testBasic($value, $result)
    {
        $filter = new BooleanFilter();
        $this->assertEquals($filter($value), $result);
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @param $localization
     * @param $boolean
     * @param $result
     * @return void
     * @dataProvider localizedBooleanProviderC
     */
    public function testLocalization($localization, $boolean, $result)
    {
        $filter = new BooleanFilter(array(
            'translations' => array($localization => $boolean)
        ));

        $this->assertEquals($filter($localization), $result);

        $filter = new BooleanFilter();
        $filter->setTranslations(array($localization => $boolean));

        $this->assertEquals($filter($localization), $result);
    }
}
