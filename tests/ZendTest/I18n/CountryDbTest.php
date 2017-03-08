<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\I18n;

use Zend\I18n\CountryDb;

class CountryDbTest extends \PHPUnit_Framework_TestCase
{
    public function testTypes()
    {
        $this->assertEquals(array(
            'official',
            'userAssigned',
            'exception',
            'transition',
            'indeterminate',
            'notUsed',
            'unAssigned',
        ), CountryDb::getTypes());
    }

    public function testGetCountriesCherryPick()
    {
        $data = array(
            'official' => array(
                'AD',
                'CW',
                'KP',
                'NZ',
                'ZW',
            ),
            'userAssigned' => array(
                'AA',
                'QP',
                'ZZ',
            ),
            'exception' => array(
                'EA',
                'UK',
            ),
            'transition' => array(
                'NT',
                'TP',
            ),
            'indeterminate' => array(
                'DY',
                'RC',
                'WV',
            ),
            'notUsed' => array(
                'BX',
                'WO',
            ),
            'unAssigned' => array(
                'AB',
                'KO',
                'PX',
                'YQ',
            ),
        );

        foreach ($data as $type => $values) {
            $countries = CountryDb::getCountries($type);
            foreach ($values as $key) {
                $this->assertContains($key, $countries);
            }
        }
    }

    public function testGetCountriesInvalidTypeThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');
        CountryDb::getCountries('foo');
    }
}
