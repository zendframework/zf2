<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use DateTime;
use stdClass;
use Zend\Validator;

/**
 * @group      Zend_Validator
 */
class DateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Validator\Date
     */
    protected $validator;

    /**
     * Creates a new Zend\Validator\Date object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->validator = new Validator\Date();
    }

    public function testSetFormatIgnoresNull()
    {
        $this->validator->setFormat(null);
        $this->assertEquals(Validator\Date::FORMAT_DEFAULT, $this->validator->getFormat());
    }

    public function datesDataProvider()
    {
        $array       = array(Validator\Date::TYPE_ARRAY);
        $integer     = array(Validator\Date::TYPE_INTEGER);
        $string      = array(Validator\Date::TYPE_STRING);
        $stringArray = array(Validator\Date::TYPE_ARRAY, Validator\Date::TYPE_STRING);

        return array(
            //    date                       format             type          isValid
            array('2007-01-01',              null,              null,         true),
            array('2007-02-28',              null,              null,         true),
            array('2007-02-29',              null,              null,         false),
            array('2008-02-29',              null,              null,         true),
            array('2007-02-30',              null,              null,         false),
            array('2007-02-99',              null,              null,         false),
            array('2007-02-99',              'Y-m-d',           null,         false),
            array('9999-99-99',              null,              null,         false),
            array('9999-99-99',              'Y-m-d',           null,         false),
            array('Jan 1 2007',              null,              null,         false),
            array('Jan 1 2007',              'M j Y',           null,         true),
            array('asdasda',                 null,              null,         false),
            array('sdgsdg',                  null,              null,         false),
            array('2007-01-01something',     null,              null,         false),
            array('something2007-01-01',     null,              null,         false),
            array('10.01.2008',              'd.m.Y',           null,         true),
            array('01 2010',                 'm Y',             null,         true),
            array('2008/10/22',              'd/m/Y',           null,         false),
            array('22/10/08',                'd/m/y',           null,         true),
            array('22/10',                   'd/m/Y',           null,         false),
            array('2007-01-01',              null,              $string,      true),
            array('2007-01-01',              null,              $integer,     false),
            // time
            array('2007-01-01T12:02:55Z',    DateTime::ISO8601, null,         true),
            array('12:02:55',                'H:i:s',           null,         true),
            array('25:02:55',                'H:i:s',           null,         false),
            // int
            array(0,                         null,              null,         true),
            array(1340677235,                null,              null,         true),
            array(1413588686,                null,              $integer,     true),
            array(1413588686,                null,              $stringArray, false),
            // Commenting out, as value appears to vary based on OS
            // array(999999999999,              null,              true),
            // array
            array(array('2012', '06', '25'), null,              null,         true),
            // 0012-06-25 is a valid date, if you want 2012, use 'y' instead of 'Y'
            array(array('12', '06', '25'),   null,              null,         true),
            array(array('2012', '06', '33'), null,              null,         false),
            array(array(1 => 1),             null,              null,         false),
            array(array('2014', '10', '18'), null,              $array,       true),
            array(array('2014', '10', '18'), null,              $string,      false),
            // DateTime
            array(new DateTime(),            null,              null,         true),
            // invalid obj
            array(new stdClass(),           null,               null,         false),
            array(new stdClass(),           null,               $string,      false),
        );
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider datesDataProvider
     */
    public function testBasic($input, $format, $type, $result)
    {
        $this->validator->setFormat($format);

        if(!empty($type)) {
            $this->validator->setAllowedTypes($type);
        }

        $this->assertEquals($result, $this->validator->isValid($input));
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->validator->getMessages());
    }

    /**
     * Ensures that the validator can handle different manual dateformats
     *
     * @group  ZF-2003
     * @return void
     */
    public function testUseManualFormat()
    {
        $this->assertTrue($this->validator->setFormat('d.m.Y')->isValid('10.01.2008'), var_export(date_get_last_errors(), 1));
        $this->assertEquals('d.m.Y', $this->validator->getFormat());

        $this->assertTrue($this->validator->setFormat('m Y')->isValid('01 2010'));
        $this->assertFalse($this->validator->setFormat('d/m/Y')->isValid('2008/10/22'));
        $this->assertTrue($this->validator->setFormat('d/m/Y')->isValid('22/10/08'));
        $this->assertFalse($this->validator->setFormat('d/m/Y')->isValid('22/10'));
        // Omitting the following assertion, as it varies from 5.3.3 to 5.3.11,
        // and there is no indication in the PHP changelog as to when or why it
        // may have changed. Leaving for posterity, to indicate original expectation.
        // $this->assertFalse($this->validator->setFormat('s')->isValid(0));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }

    public function testEqualsMessageVariables()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageVariables'),
                                     'messageVariables', $validator);
    }
}
