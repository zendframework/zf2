<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
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
                'AD' => 'Andorra',
                'CW' => 'Curaçao',
                'KP' => 'Korea, Democratic People\'s Republic of',
                'NZ' => 'New Zealand',
                'ZW' => 'Zimbabwe',
            ),
            'userAssigned' => array(
                'AA' => 'user-assigned',
                'QP' => 'user-assigned',
                'ZZ' => 'user-assigned',
            ),
            'exception' => array(
                'EA' => 'Ceuta, Melilla',
                'UK' => 'United Kingdom',
            ),
            'transition' => array(
                'NT' => 'Neutral Zone',
                'TP' => 'East Timor',
            ),
            'indeterminate' => array(
                'DY' => 'Benin',
                'RC' => 'China',
                'WV' => 'Saint Vincent',
            ),
            'notUsed' => array(
                'BX' => 'Benelux Trademarks and Design Offices',
                'WO' => 'World Intellectual Property Organization',
            ),
            'unAssigned' => array(
                'AB' => 'un-assigned',
                'KO' => 'un-assigned',
                'PX' => 'un-assigned',
                'YQ' => 'un-assigned',
            ),
        );

        foreach ($data as $type => $values) {
            $countries = CountryDb::getCountries($type);
            foreach ($values as $key => $value) {
                $this->assertEquals($value, $countries[$key]);
            }
        }
    }

    public function testGetCountriesInvalidTypeThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');
        CountryDb::getCountries('foo');
    }
}
