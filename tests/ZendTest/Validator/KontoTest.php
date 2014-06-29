<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use Zend\Validator\GermanBanking\Konto;

/**
 * @group      Zend_Validator
 */
class KontoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Konto
     */
    protected $validator;

    /**
     * Creates a new Bic object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->validator = new Konto();
    }

    public function bicDataProvider()
    {
        return array(
            //    bank          account     isValid
            array("70169464",   "1112",     true),
            array("70169464",   "67067",    true),
            array("",           "1112",     false),
            array(null,         "67067",    false),
            array("",           "123",      false),
            array(null,         "123",      false),
        );
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider bicDataProvider
     */
    public function testIsValid($bank, $input, $result)
    {
        $this->validator->setBank($bank);
        $this->assertEquals($result, $this->validator->isValid($input));
    }
}
