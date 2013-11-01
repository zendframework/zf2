<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InputFilter
 */

namespace ZendTest\InputFilter;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Filter\FilterChain;
use Zend\InputFilter\Input;
use Zend\Validator\ValidatorChain;

/**
 * @covers \Zend\InputFilter\Input
 */
class InputTest extends TestCase
{
    /**
     * @var Input
     */
    protected $input;

    public function setUp()
    {
        $this->input = new Input();
    }

    public function testAssertDefaultValues()
    {
        $this->assertNull($this->input->getName());
        $this->assertFalse($this->input->isRequired());
        $this->assertFalse($this->input->allowEmpty());
        $this->assertFalse($this->input->breakOnFailure());
    }

    public function testInjectPluginManagersIfNoneAreProvided()
    {
        $input = new Input();
        $this->assertInstanceOf('Zend\Filter\FilterChain', $input->getFilterChain());
        $this->assertInstanceOf('Zend\Validator\ValidatorChain', $input->getValidatorChain());

        $filterChain    = new FilterChain();
        $validatorChain = new ValidatorChain();
        $input          = new Input($filterChain, $validatorChain);
        $this->assertSame($filterChain, $input->getFilterChain());
        $this->assertSame($validatorChain, $input->getValidatorChain());
    }

    public function testAssertSetRequiredAddNotEmptyValidator()
    {
        $this->markTestIncomplete('To do when Zend\Validator refactoring is merged');
    }

    public function simpleDataProvider()
    {
        return array(
            // Test simplest use case (no validators nor filters)
            array(
                'value'       => 'foo',
                'required'    => false,
                'allow_empty' => false,
                'filters'     => array(),
                'validators'  => array(),
                'expected'    => true,
                'filtered'    => 'foo'
            ),

            // Test with a simple filter
            array(
                'value'       => ' foo ',
                'required'    => false,
                'allow_empty' => false,
                'filters'     => array(
                    array('name' => 'StringTrim')
                ),
                'validators'  => array(),
                'expected'    => true,
                'filtered'    => 'foo'
            ),

            // Test with a simple validator
            array(
                'value'       => 'foo',
                'required'    => false,
                'allow_empty' => false,
                'filters'     => array(),
                'validators'  => array(
                    array('name' => 'Digits')
                ),
                'expected'    => false,
                'filtered'    => null // This makes sure value is not saved if input is invalid
            ),

            // Test that assert that filters are always run before validators
            array(
                'value'       => ' foo ',
                'required'    => false,
                'allow_empty' => false,
                'filters'     => array(
                    array('name' => 'StringTrim')
                ),
                'validators'  => array(
                    array('name' => 'Alnum')
                ),
                'expected'    => true,
                'filtered'    => 'foo'
            ),

            // Test required property
            array(
                'value'       => 'foo',
                'required'    => true,
                'allow_empty' => false,
                'filters'     => array(),
                'validators'  => array(),
                'expected'    => true,
                'filtered'    => 'foo'
            ),

            array(
                'value'       => '',
                'required'    => true,
                'allow_empty' => false,
                'filters'     => array(),
                'validators'  => array(),
                'expected'    => false,
                'filtered'    => null // This makes sure value is not saved if input is invalid
            ),

            // Test allow empty works if required but empty
            array(
                'value'       => 'foo',
                'required'    => true,
                'allow_empty' => true,
                'filters'     => array(),
                'validators'  => array(),
                'expected'    => true,
                'filtered'    => 'foo'
            ),
        );
    }

    /**
     * @dataProvider simpleDataProvider
     */
    public function testRunAgainstData(
        $value,
        $required,
        $allowEmpty,
        $filters,
        $validators,
        $expected,
        $filtered
    ) {
        $this->input->setRequired($required);
        $this->input->setAllowEmpty($allowEmpty);

        if (!empty($filters)) {
            $filterChain = $this->input->getFilterChain();

            foreach ($filters as $filter) {
                // @TODO Change this when filter refactor is merged

                $name     = $filter['name'];
                $options  = isset($filter['options']) ? $filter['options'] : array();
                $priority = isset($filter['priority']) ? $filter['priority'] : 1;

                $filterChain->attachByName($name, $options, $priority);
            }
        }

        if (!empty($validators)) {
            $validatorChain = $this->input->getValidatorChain();

            foreach ($validators as $validator) {
                // @TODO Change this when validator refactor is merged

                $name     = $validator['name'];
                $options  = isset($validator['options']) ? $validator['options'] : array();
                $priority = isset($validator['priority']) ? $validator['priority'] : 1;

                $validatorChain->attachByName($name, $options);
            }
        }

        $result = $this->input->runAgainst($value);

        $this->assertInstanceOf('Zend\InputFilter\Result\InputFilterResultInterface', $result);
        $this->assertEquals($expected, $result->isValid());
        $this->assertEquals($filtered, $result->getData());
        $this->assertEquals($value, $result->getRawData());
    }
}
