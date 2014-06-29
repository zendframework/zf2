<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use Zend\Validator\GermanBanking\Blz;

/**
 * @group      Zend_Validator
 */
class BlzTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Blz
     */
    protected $validator;

    /**
     * Creates a new Blz object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->validator = new Blz();
    }

    public function blzDataProvider()
    {
        return array(
            //    blz           isValid
            array("12345",      false),
            array("",           false),
            array(null,         false),
            array("70169464",   true),
            array("10000000",   true),
        );
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider blzDataProvider
     */
    public function testIsValid($input, $result)
    {
        $this->assertEquals($result, $this->validator->isValid($input));
    }
}
