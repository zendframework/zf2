<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace ZendTest\Validator;

use Zend\Validator\InArray;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class InArrayTest extends \PHPUnit_Framework_TestCase
{
    /** @var InArray */
    protected $validator;

    protected function setUp()
    {
        $this->validator = new InArray(
            array(
                 'haystack' => array(1, 2, 3),
            )
        );
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
     * Ensures that getHaystack() returns expected value
     *
     * @return void
     */
    public function testGetHaystack()
    {
        $this->assertEquals(array(1, 2, 3), $this->validator->getHaystack());
    }

    public function testSetEmptyHaystack()
    {
        $this->validator->setHaystack(array());
        $this->setExpectedException(
            'Zend\Validator\Exception\RuntimeException',
            'haystack option is mandatory'
        );
        $this->validator->getHaystack();
    }

    /**
     * Ensures that getStrict() returns expected default value
     *
     * @return void
     */
    public function testGetStrict()
    {
        $this->assertFalse($this->validator->getStrict());
    }

    /**
     * Ensures that getRecursive() returns expected default value
     *
     * @return void
     */
    public function testGetRecursive()
    {
        $this->assertFalse($this->validator->getRecursive());
    }

    public function testSettingANewHaystack()
    {
        $this->validator->setHaystack(array(1, 'a', 2.3));
        $this->assertEquals(array(1, 'a', 2.3), $this->validator->getHaystack());
    }

    /**
     * @group ZF2-337
     */
    public function testSettingNewStrictMode()
    {
        $validator = new InArray(
            array(
                 'haystack' => array('test', 1, 'A'),
            )
        );
        $validator->setStrict(true);
        $this->assertTrue($validator->getStrict());
        $this->assertFalse($validator->isValid('b'));
        $this->assertFalse($validator->isValid('a'));
        $this->assertTrue($validator->isValid('A'));
        $this->assertFalse($validator->isValid('1'));
        $this->assertTrue($validator->isValid(1));
        $this->assertFalse($validator->isValid(0));
    }

    /**
     * Map: <value>, <group>
     * Group means the value equivalent in non-strict mode.
     *
     * @return array Map: <value>, <group>
     */
    public function valueProvider() {
        return array(
            array(0, 0),
            array('0', 0),
            array(0.0, 0),
            array('0.0', '0.0'), // Not validate against 0 or 0.0
            array('0.00', '0.00'), // Not validate against 0 or 0.0
            array(0.01, 0.01),
            array('0.01', 0.01),
            array(1, 1),
            array('1', 1),
            array('a', 'a'),
            array('0a', '0a'), // Not validate against a
            array('00', '00'), // Not validate against 0
            array('01', '01'), // Not validate against 1
            array('1a', '1a'), // Not validate against 1
        );
    }

    /**
     * @return array Return the cartesian product of ValueProvider with itself returning all the possibilities with
     * haystack and values
     */
    public function haystackValueProvider() {
        $values   = $this->valueProvider();
        $haystack = $values;
        $result = array();
        foreach ($haystack as $element) {
            foreach ($values as $value) {
                $result[] = array($element[0], $element[1], $value[0], $value[1]);
            }
        }
        return $result;
    }

    /**
     * @dataProvider haystackValueProvider
     *
     * @group ZF2-337
     * @group ZF2-411
     */
    public function testNonStrictValidation($haystack, $haystackGroup, $value, $valueGroup)
    {
        $validator = new InArray(
            array(
                 'haystack' => array($haystack),
            )
        );
        $this->assertEquals(($haystackGroup === $valueGroup), $validator->isValid($value), "Haystack: $haystack; Value: $value");
    }

    /**
     * @dataProvider haystackValueProvider
     *
     * @group ZF2-337
     * @group ZF2-411
     */
    public function testNonStrictValidationRecursive($haystack, $haystackGroup, $value, $valueGroup)
    {
        $validator = new InArray(
            array(
                 'haystack'  => array(array($haystack)),
                 'recursive' => true,
            )
        );
        $this->assertEquals(($haystackGroup === $valueGroup), $validator->isValid($value), "Haystack: $haystack; Value: $value");
    }

    public function testSettingStrictViaInitiation()
    {
        $validator = new InArray(
            array(
                 'haystack' => array('test', 1, 'A'),
                 'strict'   => true,
            )
        );
        $this->assertTrue($validator->getStrict());
    }

    public function testGettingRecursiveOption()
    {
        $this->assertFalse($this->validator->getRecursive());

        $this->validator->setRecursive(true);
        $this->assertTrue($this->validator->getRecursive());
    }

    public function testSettingRecursiveViaInitiation()
    {
        $validator = new InArray(
            array(
                 'haystack'  => array('test', 0, 'A'),
                 'recursive' => true,
            )
        );
        $this->assertTrue($validator->getRecursive());
    }

    public function testRecursiveDetection()
    {
        $validator = new InArray(
            array(
                 'haystack'  =>
                 array(
                     'firstDimension'  => array('test', 0, 'A'),
                     array('value', 2, 'a'),
                 ),
                 'recursive' => false,
            )
        );
        $this->assertFalse($validator->isValid('A'));
        $this->assertTrue($validator->isValid(array('value', 2, 'a')));

        $validator->setRecursive(true);
        $this->assertTrue($validator->isValid('A'));
        $this->assertFalse($validator->isValid(array('value', 2, 'a')));
    }

    public function testEqualsMessageTemplates()
    {
        $this->assertAttributeEquals($this->validator->getOption('messageTemplates'),
                                     'messageTemplates', $this->validator);
    }
}
