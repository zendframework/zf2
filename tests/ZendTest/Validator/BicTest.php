<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use Zend\Validator\GermanBanking\Bic;

/**
 * @group      Zend_Validator
 */
class BicTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Bic
     */
    protected $validator;

    /**
     * Creates a new Bic object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->validator = new Bic();
    }

    public function bicDataProvider()
    {
        return array(
            //    bic               isValid
            array("VZVDDED1XXX",    true),
            array("VZVDDED1",       true),
            array("VZVDDED1~~~",    false),
            array("",               false),
            array(null,             false),
        );
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider bicDataProvider
     */
    public function testIsValid($input, $result)
    {
        $this->assertEquals($result, $this->validator->isValid($input));
    }
}
