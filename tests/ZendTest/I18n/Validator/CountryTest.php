<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace ZendTest\I18n\Validator;

use Zend\I18n\Validator\Country;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class CountryTest extends \PHPUnit_Framework_TestCase
{
    protected $validator;

    public function setUp()
    {
        $this->validator = new Country();
    }

    public function testExpectedResults()
    {
        $valuesExpected = array(
            '00' => false,
            'US' => true,
            'GB' => true,
            'DE' => true,
            'ZZ' => false,
            'B'  => false,
            'ZA' => true,
        );
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
