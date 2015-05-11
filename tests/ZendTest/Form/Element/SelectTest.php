<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Form\Element;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element\Select as SelectElement;

class SelectTest extends TestCase
{
    public function testProvidesInputSpecificationForSingleSelect()
    {
        $element = new SelectElement();
        $element->setValueOptions([
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        ]);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = [
            'Zend\Validator\InArray'
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
        }
    }

    public function testValidateWorksForNestedSelectElementWithSimpleNaming()
    {
        $element = new SelectElement();
        $element->setValueOptions([
          ['label' => 'group 1', 'options' => [
            'Option 1' => 'Label 1',
            'Option 2' => 'Label 2',
            'Option 3' => 'Label 2',
          ]]]);

        $inputSpec = $element->getInputSpecification();
        $inArrayValidator = $inputSpec['validators'][0];

        $this->assertTrue($inArrayValidator->isValid('Option 1'));
        $this->assertFalse($inArrayValidator->isValid('Option 5'));
    }

    public function testValidateWorksForNestedSelectElementWithExplicitNaming()
    {
        $element = new SelectElement();
        $element->setValueOptions([
          ['label' => 'group 1', 'options' => [
            ['value' => 'Option 1', 'label'=> 'Label 1'],
            ['value' => 'Option 2', 'label'=> 'Label 2'],
            ['value' => 'Option 3', 'label'=> 'Label 3'],
          ]]]);

        $inputSpec = $element->getInputSpecification();
        $inArrayValidator = $inputSpec['validators'][0];

        $this->assertTrue($inArrayValidator->isValid('Option 1'));
        $this->assertTrue($inArrayValidator->isValid('Option 2'));
        $this->assertTrue($inArrayValidator->isValid('Option 3'));
        $this->assertFalse($inArrayValidator->isValid('Option 5'));
    }
    public function testProvidesInputSpecificationForMultipleSelect()
    {
        $element = new SelectElement();
        $element->setAttributes([
            'multiple' => true,
        ]);
        $element->setValueOptions([
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        ]);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = [
            'Zend\Validator\Explode'
        ];
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertContains($class, $expectedClasses, $class);
            switch ($class) {
                case 'Zend\Validator\Explode':
                    $this->assertInstanceOf('Zend\Validator\InArray', $validator->getValidator());
                    break;
                default:
                    break;
            }
        }
    }

    public function selectOptionsDataProvider()
    {
        return [
            [
                ['foo', 'bar'],
                [
                    'foo' => 'My Foo Label',
                    'bar' => 'My Bar Label',
                ]
            ],
            [
                ['foo', 'bar'],
                [
                    0 => ['label' => 'My Foo Label', 'value' => 'foo'],
                    1 => ['label' => 'My Bar Label', 'value' => 'bar'],
                ]
            ],
        ];
    }

    /**
     * @dataProvider selectOptionsDataProvider
     */
    public function testInArrayValidationOfOptions($valueTests, $options)
    {
        $element = new SelectElement('my-select');
        $element->setValueOptions($options);
        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $inArrayValidator = $inputSpec['validators'][0];
        $this->assertInstanceOf('Zend\Validator\InArray', $inArrayValidator);
        foreach ($valueTests as $valueToTest) {
            $this->assertTrue($inArrayValidator->isValid($valueToTest));
        }
    }

    /**
     * Testing that InArray Validator Haystack is Updated if the Options
     * are added after the validator is attached
     *
     * @dataProvider selectOptionsDataProvider
     */
    public function testInArrayValidatorHaystakIsUpdated($valueTests, $options)
    {
        $element = new SelectElement('my-select');
        $inputSpec = $element->getInputSpecification();

        $inArrayValidator = $inputSpec['validators'][0];
        $this->assertInstanceOf('Zend\Validator\InArray', $inArrayValidator);

        $element->setValueOptions($options);
        $haystack=$inArrayValidator->getHaystack();
        $this->assertCount(count($options), $haystack);
    }


    public function testOptionsHasArrayOnConstruct()
    {
        $element = new SelectElement();
        $this->assertInternalType('array', $element->getValueOptions());
    }

    public function testDeprecateOptionsInAttributes()
    {
        $element = new SelectElement();
        $valueOptions = [
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        ];
        $element->setAttributes([
            'multiple' => true,
            'options'  => $valueOptions,
        ]);
        $this->assertEquals($valueOptions, $element->getValueOptions());
    }

    public function testSetOptionsOptions()
    {
        $element = new SelectElement();
        $element->setOptions([
                                  'value_options' => ['bar' => 'baz'],
                                  'options' => ['foo' => 'bar'],
                                  'empty_option' => ['baz' => 'foo'],
                             ]);
        $this->assertEquals(['bar' => 'baz'], $element->getOption('value_options'));
        $this->assertEquals(['foo' => 'bar'], $element->getOption('options'));
        $this->assertEquals(['baz' => 'foo'], $element->getOption('empty_option'));
    }

    public function testDisableInputSpecification()
    {
        $element = new SelectElement();
        $element->setValueOptions([
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        ]);
        $element->setDisableInArrayValidator(true);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayNotHasKey('validators', $inputSpec);
    }

    public function testUnsetValueOption()
    {
        $element = new SelectElement();
        $element->setValueOptions([
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        ]);
        $element->unsetValueOption('Option 2');

        $valueOptions = $element->getValueOptions();
        $this->assertArrayNotHasKey('Option 2', $valueOptions);
    }

    public function testUnsetUndefinedValueOption()
    {
        $element = new SelectElement();
        $element->setValueOptions([
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
        ]);
        $element->unsetValueOption('Option Undefined');

        $valueOptions = $element->getValueOptions();
        $this->assertArrayNotHasKey('Option Undefined', $valueOptions);
    }

    public function testSetOptionsToSelectMultiple()
    {
        $element = new SelectElement(null, [
            'label' => 'Importance',
            'use_hidden_element' => true,
            'unselected_value' => 'empty',
            'value_options' => [
                'foo' => 'Foo',
                'bar' => 'Bar'
            ],
        ]);
        $element->setAttributes(['multiple' => 'multiple']);

        $this->assertTrue($element->isMultiple());
        $this->assertTrue($element->useHiddenElement());
        $this->assertEquals('empty', $element->getUnselectedValue());
    }

    public function testProvidesInputSpecificationForMultipleSelectWithUseHiddenElement()
    {
        $element = new SelectElement();
        $element
            ->setUseHiddenElement(true)
            ->setAttributes([
                'multiple' => true,
            ]);

        $inputSpec = $element->getInputSpecification();

        $this->assertArrayHasKey('allow_empty', $inputSpec);
        $this->assertTrue($inputSpec['allow_empty']);
        $this->assertArrayHasKey('continue_if_empty', $inputSpec);
        $this->assertTrue($inputSpec['continue_if_empty']);
    }
}
