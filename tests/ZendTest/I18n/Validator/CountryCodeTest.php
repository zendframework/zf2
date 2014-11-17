<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\I18n\Validator;

use Zend\I18n\CountryDb;
use Zend\I18n\Validator\CountryCode;

class CountryCodeTest extends \PHPUnit_Framework_TestCase
{
    protected $validator;

    public function setUp()
    {
        $this->validator = new CountryCode();
    }

    public function testOfficialIsDefault()
    {
        foreach (CountryDb::getTypes() as $type) {
            $assertEqualsValue = ($type == 'official') ? true : false;

            foreach (CountryDb::getCountries($type) as $country) {
                $this->assertEquals($assertEqualsValue, $this->validator->isValid($country));
            }
        }
    }

    public function testLoadCountryTypes()
    {
        $types = array('userAssigned', 'exception');
        $this->validator->loadCountryTypes($types);

        foreach (CountryDb::getTypes() as $type) {
            $assertEqualsValue = (in_array($type, $types)) ? true : false;

            foreach (CountryDb::getCountries($type) as $country) {
                $this->assertEquals($assertEqualsValue, $this->validator->isValid($country));
            }
        }
    }

    public function testSetCountries()
    {
        $this->validator->setCountries(array('AB', 'AZ'));

        $this->assertTrue($this->validator->isValid('AB'));
        $this->assertTrue($this->validator->isValid('AZ'));
        $this->assertFalse($this->validator->isValid('US'));
    }

    public function testInvalidTypes()
    {
        $values = array(
            array(),
            new \stdClass,
        );

        foreach ($values as $value) {
            $this->assertFalse($this->validator->isValid($value));
        }
    }
}
