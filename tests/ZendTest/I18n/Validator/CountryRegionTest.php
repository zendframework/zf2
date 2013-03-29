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

use Zend\I18n\Validator\CountryRegion;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class CountryRegionTest extends \PHPUnit_Framework_TestCase
{
    protected $validator;

    public function setUp()
    {
        $this->validator = new CountryRegion();
    }

    public function testExpectedResults()
    {
        $valuesExpected = array(
            'US' => array(
                'US-CA' => true,
                'US-ZZ' => false,
                'US-0'  => false,
            ),
            'BF' => array(
                'BAL' => false,
                'BF-BAL' => true,
                '00' => false,
            ),
            'DE' => array(
                'DE-BB' => true,
                'DA-MV' => false,
                'MW' => false,
            ),
        );

        foreach ($valuesExpected as $country => $values) {
            $this->validator->setCountry($country);
            foreach ($values as $value => $expected) {
                $this->assertEquals($expected, $this->validator->isValid($value));
            }
        }
    }
}
