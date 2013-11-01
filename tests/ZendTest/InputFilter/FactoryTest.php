<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\InputFilter;

use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilterPluginManager;

/**
 * @covers \Zend\InputFilter\Factory
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InputFilterPluginManager
     */
    protected $inputFilterPluginManager;

    /**
     * @var Factory
     */
    protected $factory;

    public function setUp()
    {
        $this->inputFilterPluginManager = new InputFilterPluginManager();
        $this->factory                  = new Factory($this->inputFilterPluginManager);
    }

    public function specificationProvider()
    {
        return array(
            // Create a single input
            array(
                'specifications' => array(
                    'type'             => 'Zend\InputFilter\Input',
                    'name'             => 'foo',
                    'required'         => true,
                    'allow_empty'      => true,
                    'break_on_failure' => true,
                    // @TODO: add filters when Filter refactoring is merged
                    // @TODO: add validators when Validator refactoring is merged
                )
            ),

            // Create a single input with Traversable
            array(
                'specifications' => new \ArrayIterator(array(
                    'type'             => 'Zend\InputFilter\Input',
                    'name'             => 'foo',
                    'required'         => true,
                    'allow_empty'      => true,
                    'break_on_failure' => true,
                    // @TODO: add filters when Filter refactoring is merged
                    // @TODO: add validators when Validator refactoring is merged
                ))
            ),

            // Create an input collection
            array(
                'specifications' => array(
                    'type'             => 'Zend\InputFilter\InputCollection',
                    'name'             => 'foo',
                    'required'         => true,
                    'allow_empty'      => true,
                    'break_on_failure' => true,
                    // @TODO: add filters when Filter refactoring is merged
                    // @TODO: add validators when Validator refactoring is merged
                )
            ),

            // Create an input collection with Traversable
            array(
                'specifications' => new \ArrayIterator(array(
                    'type'             => 'Zend\InputFilter\InputCollection',
                    'name'             => 'foo',
                    'required'         => true,
                    'allow_empty'      => true,
                    'break_on_failure' => true,
                    // @TODO: add filters when Filter refactoring is merged
                    // @TODO: add validators when Validator refactoring is merged
                ))
            ),
        );
    }

    /**
     * @dataProvider specificationProvider
     */
    public function testCanCreateFromSpecification($specifications)
    {
        $inputOrCollection = $this->factory->createFromSpecification($specifications);

        $this->assertInstanceOf($specifications['type'], $inputOrCollection);
        $this->assertEquals($specifications['name'], $inputOrCollection->getName());
        $this->assertEquals($specifications['required'], $inputOrCollection->isRequired());
        $this->assertEquals($specifications['allow_empty'], $inputOrCollection->allowEmpty());
        $this->assertEquals($specifications['break_on_failure'], $inputOrCollection->breakOnFailure());

        // @TODO: test filters
        // @TODO: test validators
    }

    public function testCreateInputByDefault()
    {
        $specification = array(
            'required' => true
        );

        $input = $this->factory->createFromSpecification($specification);

        $this->assertInstanceOf('Zend\InputFilter\Input', $input);
        $this->assertTrue($input->isRequired());
    }

    public function testCanDelegateCustomOptions()
    {
        $specification = array(
            'type'          => 'ZendTest\InputFilter\Asset\CustomInput',
            'custom_option' => 'foo'
        );

        $this->inputFilterPluginManager->setInvokableClass(
            'ZendTest\InputFilter\Asset\CustomInput',
            'ZendTest\InputFilter\Asset\CustomInput'
        );

        $input = $this->factory->createFromSpecification($specification);

        $this->assertInstanceOf('ZendTest\InputFilter\Asset\CustomInput', $input);
        $this->assertEquals($specification['custom_option'], $input->getCustomOption());
    }

    public function testCanOverwriteBuiltInFilters()
    {
        $this->markTestIncomplete('Todo when filters refactor is merged');
    }

    public function testCanOverwriteBuiltInValidators()
    {
        $this->markTestIncomplete('Todo when validators refactor is merged');
    }

    public function testCanCreateComplexInputCollection()
    {
        $specification = array(
            'type'     => 'Zend\InputFilter\InputCollection',
            'name'     => 'first',
            'required' => true,
            'children' => array(
                array(
                    'type'     => 'Zend\InputFilter\Input',
                    'name'     => 'second',
                    'required' => true
                ),
                array(
                    'type'     => 'Zend\InputFilter\InputCollection',
                    'name'     => 'third',
                    'required' => true,
                    'children' => array(
                        array(
                            'type'     => 'Zend\InputFilter\InputCollection',
                            'name'     => 'inception',
                            'children' => array(
                                'type'     => 'Zend\InputFilter\Input',
                                'name'     => 'fourth',
                                'required' => true
                            )
                        )
                    )
                )
            )
        );

        $inputCollection = $this->factory->createFromSpecification($specification);

        $this->assertInstanceOf('Zend\InputFilter\InputCollection', $inputCollection);
        $this->assertSame('first', $inputCollection->getName());
        $this->assertTrue($inputCollection->isRequired());
    }
} 