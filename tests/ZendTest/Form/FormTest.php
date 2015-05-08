<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form;

use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\Form\Element;
use Zend\Form\Factory;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\BaseInputFilter;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFilterFactory;
use Zend\Stdlib\Hydrator;
use ZendTest\Form\TestAsset\Entity;
use ZendTest\Form\TestAsset\HydratorAwareModel;

class FormTest extends TestCase
{
    /**
     * @var Form
     */
    protected $form;

    public function setUp()
    {
        $this->form = new Form();
    }

    public function getComposedEntity()
    {
        $address = new TestAsset\Entity\Address();
        $address->setStreet('1 Rue des Champs Elysées');

        $city = new TestAsset\Entity\City();
        $city->setName('Paris');
        $city->setZipCode('75008');

        $country = new TestAsset\Entity\Country();
        $country->setName('France');
        $country->setContinent('Europe');

        $city->setCountry($country);
        $address->setCity($city);

        return $address;
    }

    public function getOneToManyEntity()
    {
        $product = new TestAsset\Entity\Product();
        $product->setName('Chair');
        $product->setPrice(10);

        $firstCategory = new TestAsset\Entity\Category();
        $firstCategory->setName('Office');

        $secondCategory = new TestAsset\Entity\Category();
        $secondCategory->setName('Armchair');

        $product->setCategories([$firstCategory, $secondCategory]);

        return $product;
    }

    public function populateHydratorStrategyForm()
    {
        $this->form->add(new Element('entities'));
    }

    public function populateForm()
    {
        $this->form->add(new Element('foo'));
        $this->form->add(new Element('bar'));

        $fieldset = new Fieldset('foobar');
        $fieldset->add(new Element('foo'));
        $fieldset->add(new Element('bar'));
        $this->form->add($fieldset);

        $inputFilterFactory = new InputFilterFactory();
        $inputFilter = $inputFilterFactory->createInputFilter([
            'foo' => [
                'name'       => 'foo',
                'required'   => false,
                'validators' => [
                    [
                        'name' => 'not_empty',
                    ],
                    [
                        'name' => 'string_length',
                        'options' => [
                            'min' => 3,
                            'max' => 5,
                        ],
                    ],
                ],
            ],
            'bar' => [
                'allow_empty' => true,
                'filters'     => [
                    [
                        'name' => 'string_trim',
                    ],
                    [
                        'name' => 'string_to_lower',
                        'options' => [
                            'encoding' => 'ISO-8859-1',
                        ],
                    ],
                ],
            ],
            'foobar' => [
                'type'   => 'Zend\InputFilter\InputFilter',
                'foo' => [
                    'name'       => 'foo',
                    'required'   => true,
                    'validators' => [
                        [
                            'name' => 'not_empty',
                        ],
                        [
                            'name' => 'string_length',
                            'options' => [
                                'min' => 3,
                                'max' => 5,
                            ],
                        ],
                    ],
                ],
                'bar' => [
                    'allow_empty' => true,
                    'filters'     => [
                        [
                            'name' => 'string_trim',
                        ],
                        [
                            'name' => 'string_to_lower',
                            'options' => [
                                'encoding' => 'ISO-8859-1',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
        $this->form->setInputFilter($inputFilter);
    }

    public function testInputFilterPresentByDefault()
    {
        $this->assertNotNull($this->form->getInputFilter());
    }

    public function testCanComposeAnInputFilter()
    {
        $filter = new InputFilter();
        $this->form->setInputFilter($filter);
        $this->assertSame($filter, $this->form->getInputFilter());
    }

    /**
     * @expectedException Zend\Form\Exception\InvalidElementException
     */
    public function testShouldThrowExceptionWhenGetInvalidElement()
    {
        $this->form->get('doesnt_exist');
    }

    public function testDefaultNonRequiredInputFilterIsSet()
    {
        $this->form->add(new Element('foo'));
        $inputFilter = $this->form->getInputFilter();
        $fooInput = $inputFilter->get('foo');
        $this->assertFalse($fooInput->isRequired());
    }

    public function testInputProviderInterfaceAddsInputFilters()
    {
        $form = new TestAsset\InputFilterProvider();

        $inputFilter = $form->getInputFilter();
        $fooInput = $inputFilter->get('foo');
        $this->assertTrue($fooInput->isRequired());
    }

    public function testInputProviderInterfaceAddsInputFiltersOnlyForSelf()
    {
        $form = new TestAsset\InputFilterProviderWithFieldset();

        $inputFilter = $form->getInputFilter();
        $fieldsetInputFilter = $inputFilter->get('basic_fieldset');
        $this->assertFalse($fieldsetInputFilter->has('foo'));
    }

    public function testCallingIsValidRaisesExceptionIfNoDataSet()
    {
        $this->setExpectedException('Zend\Form\Exception\DomainException');
        $this->form->isValid();
    }

    public function testHasValidatedFlag()
    {
        if (!extension_loaded('intl')) {
            // Required by \Zend\I18n\Validator\IsFloat
            $this->markTestSkipped('ext/intl not enabled');
        }

        $form = new TestAsset\NewProductForm();

        $this->assertFalse($form->hasValidated());

        $form->setData([]);
        $form->isValid();


        $this->assertTrue($form->hasValidated());
    }

    public function testValidatesEntireDataSetByDefault()
    {
        $this->populateForm();
        $invalidSet = [
            'foo' => 'a',
            'bar' => 'always valid',
            'foobar' => [
                'foo' => 'a',
                'bar' => 'always valid',
            ],
        ];
        $this->form->setData($invalidSet);
        $this->assertFalse($this->form->isValid());

        $validSet = [
            'foo' => 'abcde',
            'bar' => 'always valid',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => 'always valid',
            ],
        ];
        $this->form->setData($validSet);
        $this->assertTrue($this->form->isValid());
    }

    public function testSpecifyingValidationGroupForcesPartialValidation()
    {
        if (!extension_loaded('intl')) {
            // Required by \Zend\I18n\Validator\IsFloat
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->populateForm();
        $invalidSet = [
            'foo' => 'a',
        ];
        $this->form->setValidationGroup('foo');
        $this->form->setData($invalidSet);
        $this->assertFalse($this->form->isValid());

        $validSet = [
            'foo' => 'abcde',
        ];
        $this->form->setData($validSet);
        $this->assertTrue($this->form->isValid());
    }

    public function testSpecifyingValidationGroupForNestedFieldsetsForcesPartialValidation()
    {
        if (!extension_loaded('intl')) {
            // Required by \Zend\I18n\Validator\IsFloat
            $this->markTestSkipped('ext/intl not enabled');
        }

        $form = new TestAsset\NewProductForm();
        $form->setData([
            'product' => [
                'name' => 'Chair'
            ]
        ]);

        $this->assertFalse($form->isValid());

        $form->setValidationGroup([
            'product' => [
                'name'
            ]
        ]);

        $this->assertTrue($form->isValid());
    }

    public function testSettingValidateAllFlagAfterPartialValidationForcesFullValidation()
    {
        $this->populateForm();
        $this->form->setValidationGroup('foo');

        $validSet = [
            'foo' => 'abcde',
        ];
        $this->form->setData($validSet);
        $this->form->setValidationGroup(Form::VALIDATE_ALL);
        $this->assertFalse($this->form->isValid());
        $messages = $this->form->getMessages();
        $this->assertArrayHasKey('foobar', $messages, var_export($messages, 1));
    }

    public function testSetValidationGroupWithNoArgumentsRaisesException()
    {
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $this->form->setValidationGroup();
    }

    public function testCallingGetDataPriorToValidationRaisesException()
    {
        $this->setExpectedException('Zend\Form\Exception\DomainException');
        $this->form->getData();
    }

    public function testAttemptingToValidateWithNoInputFilterAttachedRaisesException()
    {
        $this->setExpectedException('Zend\Form\Exception\DomainException');
        $this->form->isValid();
    }

    public function testCanRetrieveDataWithoutErrorsFollowingValidation()
    {
        $this->populateForm();
        $validSet = [
            'foo' => 'abcde',
            'bar' => ' ALWAYS valid ',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => ' ALWAYS valid',
            ],
        ];
        $this->form->setData($validSet);
        $this->form->isValid();

        $data = $this->form->getData();
        $this->assertInternalType('array', $data);
    }

    /**
     * @group ZF2-336
     */
    public function testCanAddFileEnctypeAttribute()
    {
        $file = new Element\File('file_resource');
        $file
            ->setOptions([])
            ->setLabel('File');
        $this->form->add($file);

        $this->form->prepare();
        $enctype = $this->form->getAttribute('enctype');
        $this->assertNotEmpty($enctype);
        $this->assertEquals($enctype, 'multipart/form-data');
    }

    /**
     * @group ZF2-336
     */
    public function testCanAddFileEnctypeFromCollectionAttribute()
    {
        $file = new Element\File('file_resource');
        $file
            ->setOptions([])
            ->setLabel('File');

        $fileCollection = new Element\Collection('collection');
        $fileCollection->setOptions([
             'count' => 2,
             'allow_add' => false,
             'allow_remove' => false,
             'target_element' => $file,
        ]);
        $this->form->add($fileCollection);

        $this->form->prepare();
        $enctype = $this->form->getAttribute('enctype');
        $this->assertNotEmpty($enctype);
        $this->assertEquals($enctype, 'multipart/form-data');
    }

    public function testCallingGetDataReturnsNormalizedDataByDefault()
    {
        $this->populateForm();
        $validSet = [
            'foo' => 'abcde',
            'bar' => ' ALWAYS valid ',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => ' ALWAYS valid',
            ],
        ];
        $this->form->setData($validSet);
        $this->form->isValid();
        $data = $this->form->getData();

        $expected = [
            'foo' => 'abcde',
            'bar' => 'always valid',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => 'always valid',
            ],
        ];
        $this->assertEquals($expected, $data);
    }

    public function testAllowsReturningRawValuesViaGetData()
    {
        $this->populateForm();
        $validSet = [
            'foo' => 'abcde',
            'bar' => ' ALWAYS valid ',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => ' ALWAYS valid',
            ],
        ];
        $this->form->setData($validSet);
        $this->form->isValid();
        $data = $this->form->getData(Form::VALUES_RAW);
        $this->assertEquals($validSet, $data);
    }

    public function testGetDataReturnsBoundModel()
    {
        $model = new stdClass;
        $this->form->setHydrator(new Hydrator\ObjectProperty());
        $this->populateForm();
        $this->form->setData([]);
        $this->form->bind($model);
        $this->form->isValid();
        $data = $this->form->getData();
        $this->assertSame($model, $data);
    }

    public function testGetDataCanReturnValuesAsArrayWhenModelIsBound()
    {
        $model = new stdClass;
        $validSet = [
            'foo' => 'abcde',
            'bar' => 'always valid',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => 'always valid',
            ],
        ];
        $this->populateForm();
        $this->form->setHydrator(new Hydrator\ObjectProperty());
        $this->form->bind($model);
        $this->form->setData($validSet);
        $this->form->isValid();
        $data = $this->form->getData(Form::VALUES_AS_ARRAY);
        $this->assertEquals($validSet, $data);
    }

    public function testValuesBoundToModelAreNormalizedByDefault()
    {
        $model = new stdClass;
        $validSet = [
            'foo' => 'abcde',
            'bar' => ' ALWAYS valid ',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => ' always VALID',
            ],
        ];
        $this->populateForm();
        $this->form->setHydrator(new Hydrator\ObjectProperty());
        $this->form->bind($model);
        $this->form->setData($validSet);
        $this->form->isValid();

        $this->assertObjectHasAttribute('foo', $model);
        $this->assertEquals($validSet['foo'], $model->foo);
        $this->assertObjectHasAttribute('bar', $model);
        $this->assertEquals('always valid', $model->bar);
        $this->assertObjectHasAttribute('foobar', $model);
        $this->assertEquals([
            'foo' => 'abcde',
            'bar' => 'always valid',
        ], $model->foobar);
    }

    public function testCanBindRawValuesToModel()
    {
        $model = new stdClass;
        $validSet = [
            'foo' => 'abcde',
            'bar' => ' ALWAYS valid ',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => ' always VALID',
            ],
        ];
        $this->populateForm();
        $this->form->setHydrator(new Hydrator\ObjectProperty());
        $this->form->bind($model, Form::VALUES_RAW);
        $this->form->setData($validSet);
        $this->form->isValid();

        $this->assertObjectHasAttribute('foo', $model);
        $this->assertEquals($validSet['foo'], $model->foo);
        $this->assertObjectHasAttribute('bar', $model);
        $this->assertEquals(' ALWAYS valid ', $model->bar);
        $this->assertObjectHasAttribute('foobar', $model);
        $this->assertEquals([
            'foo' => 'abcde',
            'bar' => ' always VALID',
        ], $model->foobar);
    }

    public function testGetDataReturnsSubsetOfDataWhenValidationGroupSet()
    {
        $validSet = [
            'foo' => 'abcde',
            'bar' => ' ALWAYS valid ',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => ' always VALID',
            ],
        ];
        $this->populateForm();
        $this->form->setValidationGroup('foo');
        $this->form->setData($validSet);
        $this->form->isValid();
        $data = $this->form->getData();
        $this->assertInternalType('array', $data);
        $this->assertEquals(1, count($data));
        $this->assertArrayHasKey('foo', $data);
        $this->assertEquals('abcde', $data['foo']);
    }

    public function testSettingValidationGroupBindsOnlyThoseValuesToModel()
    {
        $model = new stdClass;
        $validSet = [
            'foo' => 'abcde',
            'bar' => ' ALWAYS valid ',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => ' always VALID',
            ],
        ];
        $this->populateForm();
        $this->form->setHydrator(new Hydrator\ObjectProperty());
        $this->form->bind($model);
        $this->form->setData($validSet);
        $this->form->setValidationGroup('foo');
        $this->form->isValid();

        $this->assertObjectHasAttribute('foo', $model);
        $this->assertEquals('abcde', $model->foo);
        $this->assertObjectNotHasAttribute('bar', $model);
        $this->assertObjectNotHasAttribute('foobar', $model);
    }

    public function testCanBindModelsToArraySerializableObjects()
    {
        $model = new TestAsset\Model();
        $validSet = [
            'foo' => 'abcde',
            'bar' => 'always valid',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => 'always valid',
            ],
        ];
        $this->populateForm();
        $this->form->setHydrator(new Hydrator\ArraySerializable());
        $this->form->bind($model);
        $this->form->setData($validSet);
        $this->form->isValid();

        $this->assertEquals('abcde', $model->foo);
        $this->assertEquals('always valid', $model->bar);
        $this->assertEquals([
            'foo' => 'abcde',
            'bar' => 'always valid',
        ], $model->foobar);
    }

    public function testSetsInputFilterToFilterFromBoundModelIfModelImplementsInputLocatorAware()
    {
        $model = new TestAsset\ValidatingModel();
        $model->setInputFilter(new InputFilter());
        $this->populateForm();
        $this->form->bind($model);
        $this->assertSame($model->getInputFilter(), $this->form->getInputFilter());
    }

    public function testSettingDataShouldSetElementValues()
    {
        $this->populateForm();
        $data = [
            'foo' => 'abcde',
            'bar' => 'always valid',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => 'always valid',
            ],
        ];
        $this->form->setData($data);

        $fieldset = $this->form->get('foobar');
        foreach (['foo', 'bar'] as $name) {
            $element = $this->form->get($name);
            $this->assertEquals($data[$name], $element->getValue());

            $element = $fieldset->get($name);
            $this->assertEquals($data[$name], $element->getValue());
        }
    }

    public function testElementValuesArePopulatedFollowingBind()
    {
        $this->populateForm();
        $object = new stdClass();
        $object->foo = 'foobar';
        $object->bar = 'barbaz';
        $this->form->setHydrator(new Hydrator\ObjectProperty());
        $this->form->bind($object);

        $foo = $this->form->get('foo');
        $this->assertEquals('foobar', $foo->getValue());
        $bar = $this->form->get('bar');
        $this->assertEquals('barbaz', $bar->getValue());
    }

    public function testUsesBoundObjectAsDataSourceWhenNoDataSet()
    {
        $this->populateForm();
        $object         = new stdClass();
        $object->foo    = 'foos';
        $object->bar    = 'bar';
        $object->foobar = [
            'foo' => 'foos',
            'bar' => 'bar',
        ];
        $this->form->setHydrator(new Hydrator\ObjectProperty());
        $this->form->bind($object);

        $this->assertTrue($this->form->isValid());
    }

    public function testUsesBoundObjectHydratorToPopulateForm()
    {
        $this->populateForm();
        $object = new HydratorAwareModel();
        $object->setFoo('fooValue');
        $object->setBar('barValue');

        $this->form->bind($object);
        $foo = $this->form->get('foo');
        $this->assertEquals('fooValue', $foo->getValue());
        $bar = $this->form->get('bar');
        $this->assertEquals('barValue', $bar->getValue());
    }

    public function testBindOnValidateIsTrueByDefault()
    {
        $this->assertTrue($this->form->bindOnValidate());
    }

    public function testCanDisableBindOnValidateFunctionality()
    {
        $this->form->setBindOnValidate(false);
        $this->assertFalse($this->form->bindOnValidate());
    }

    public function testCallingBindValuesWhenBindOnValidateIsDisabledPopulatesBoundObject()
    {
        $model = new stdClass;
        $validSet = [
            'foo' => 'abcde',
            'bar' => ' ALWAYS valid ',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => ' always VALID',
            ],
        ];
        $this->populateForm();
        $this->form->setHydrator(new Hydrator\ObjectProperty());
        $this->form->setBindOnValidate(false);
        $this->form->bind($model);
        $this->form->setData($validSet);
        $this->form->isValid();

        $this->assertObjectNotHasAttribute('foo', $model);
        $this->assertObjectNotHasAttribute('bar', $model);
        $this->assertObjectNotHasAttribute('foobar', $model);

        $this->form->bindValues();

        $this->assertObjectHasAttribute('foo', $model);
        $this->assertEquals($validSet['foo'], $model->foo);
        $this->assertObjectHasAttribute('bar', $model);
        $this->assertEquals('always valid', $model->bar);
        $this->assertObjectHasAttribute('foobar', $model);
        $this->assertEquals([
            'foo' => 'abcde',
            'bar' => 'always valid',
        ], $model->foobar);
    }

    public function testHasFactoryComposedByDefault()
    {
        $factory = $this->form->getFormFactory();
        $this->assertInstanceOf('Zend\Form\Factory', $factory);
    }

    public function testCanComposeFactory()
    {
        $factory = new Factory();
        $this->form->setFormFactory($factory);
        $this->assertSame($factory, $this->form->getFormFactory());
    }

    public function testCanAddElementsUsingSpecs()
    {
        $this->form->add([
            'name'       => 'foo',
            'attributes' => [
                'type'         => 'text',
                'class'        => 'foo-class',
                'data-js-type' => 'my.form.text',
            ],
        ]);
        $this->assertTrue($this->form->has('foo'));
        $element = $this->form->get('foo');
        $this->assertInstanceOf('Zend\Form\ElementInterface', $element);
        $this->assertEquals('foo', $element->getName());
        $this->assertEquals('text', $element->getAttribute('type'));
        $this->assertEquals('foo-class', $element->getAttribute('class'));
        $this->assertEquals('my.form.text', $element->getAttribute('data-js-type'));
    }

    public function testCanAddFieldsetsUsingSpecs()
    {
        $this->form->add([
            'type'       => 'Zend\Form\Fieldset',
            'name'       => 'foo',
            'attributes' => [
                'type'         => 'fieldset',
                'class'        => 'foo-class',
                'data-js-type' => 'my.form.fieldset',
            ],
        ]);
        $this->assertTrue($this->form->has('foo'));
        $fieldset = $this->form->get('foo');
        $this->assertInstanceOf('Zend\Form\FieldsetInterface', $fieldset);
        $this->assertEquals('foo', $fieldset->getName());
        $this->assertEquals('fieldset', $fieldset->getAttribute('type'));
        $this->assertEquals('foo-class', $fieldset->getAttribute('class'));
        $this->assertEquals('my.form.fieldset', $fieldset->getAttribute('data-js-type'));
    }

    public function testFormAsFieldsetWillBindValuesToObject()
    {
        $parentForm        = new Form('parent');
        $parentFormObject  = new \ArrayObject(['parentId' => null]);
        $parentFormElement = new Element('parentId');
        $parentForm->setObject($parentFormObject);
        $parentForm->add($parentFormElement);

        $childForm        = new Form('child');
        $childFormObject  = new \ArrayObject(['childId' => null]);
        $childFormElement = new Element('childId');
        $childForm->setObject($childFormObject);
        $childForm->add($childFormElement);

        $parentForm->add($childForm);

        $data = [
            'parentId' => 'mpinkston was here',
            'child' => [
                'childId' => 'testing 123'
            ]
        ];

        $parentForm->setData($data);
        $this->assertTrue($parentForm->isValid());
        $this->assertEquals($data['parentId'], $parentFormObject['parentId']);
        $this->assertEquals($data['child']['childId'], $childFormObject['childId']);
    }

    public function testWillUseInputSpecificationFromElementInInputFilterIfNoMatchingInputFound()
    {
        $element = new TestAsset\ElementWithFilter('foo');
        $filter  = new InputFilter();
        $this->form->setInputFilter($filter);
        $this->form->add($element);

        $test = $this->form->getInputFilter();
        $this->assertSame($filter, $test);
        $this->assertTrue($filter->has('foo'));
        $input = $filter->get('foo');
        $filters = $input->getFilterChain();
        $this->assertEquals(1, count($filters));
        $validators = $input->getValidatorChain();
        $this->assertEquals(2, count($validators));
        $this->assertTrue($input->isRequired());
        $this->assertEquals('foo', $input->getName());
    }

    public function testWillUseInputFilterSpecificationFromFieldsetInInputFilterIfNoMatchingInputFilterFound()
    {
        $fieldset = new TestAsset\FieldsetWithInputFilter('set');
        $filter   = new InputFilter();
        $this->form->setInputFilter($filter);
        $this->form->add($fieldset);

        $test = $this->form->getInputFilter();
        $this->assertSame($filter, $test);
        $this->assertTrue($filter->has('set'));
        $input = $filter->get('set');
        $this->assertInstanceOf('Zend\InputFilter\InputFilterInterface', $input);
        $this->assertEquals(2, count($input));
        $this->assertTrue($input->has('foo'));
        $this->assertTrue($input->has('bar'));
    }

    public function testWillPopulateSubInputFilterFromInputSpecificationsOnFieldsetElements()
    {
        $element        = new TestAsset\ElementWithFilter('foo');
        $fieldset       = new Fieldset('set');
        $filter         = new InputFilter();
        $fieldsetFilter = new InputFilter();
        $fieldset->add($element);
        $filter->add($fieldsetFilter, 'set');
        $this->form->setInputFilter($filter);
        $this->form->add($fieldset);

        $test = $this->form->getInputFilter();
        $this->assertSame($filter, $test);
        $test = $filter->get('set');
        $this->assertSame($fieldsetFilter, $test);

        $this->assertEquals(1, count($fieldsetFilter));
        $this->assertTrue($fieldsetFilter->has('foo'));

        $input = $fieldsetFilter->get('foo');
        $this->assertInstanceOf('Zend\InputFilter\InputInterface', $input);
        $filters = $input->getFilterChain();
        $this->assertEquals(1, count($filters));
        $validators = $input->getValidatorChain();
        $this->assertEquals(2, count($validators));
        $this->assertTrue($input->isRequired());
        $this->assertEquals('foo', $input->getName());
    }

    public function testWillUseFormInputFilterOverrideOverInputSpecificationFromElement()
    {
        $element = new TestAsset\ElementWithFilter('foo');
        $filter  = new InputFilter();
        $filterFactory = new InputFilterFactory();
        $filter = $filterFactory->createInputFilter([
            'foo' => [
                'name'       => 'foo',
                'required'   => false,
            ],
        ]);
        $this->form->setPreferFormInputFilter(true);
        $this->form->setInputFilter($filter);
        $this->form->add($element);

        $test = $this->form->getInputFilter();
        $this->assertSame($filter, $test);
        $this->assertTrue($filter->has('foo'));
        $input = $filter->get('foo');
        $filters = $input->getFilterChain();
        $this->assertEquals(0, count($filters));
        $validators = $input->getValidatorChain();
        $this->assertEquals(0, count($validators));
        $this->assertFalse($input->isRequired());
        $this->assertEquals('foo', $input->getName());
    }

    public function testDisablingUseInputFilterDefaultsFlagDisablesInputFilterScanning()
    {
        $element        = new TestAsset\ElementWithFilter('foo');
        $fieldset       = new Fieldset('set');
        $filter         = new InputFilter();
        $fieldsetFilter = new InputFilter();
        $fieldset->add($element);
        $filter->add($fieldsetFilter, 'set');
        $this->form->setInputFilter($filter);
        $this->form->add($fieldset);

        $this->form->setUseInputFilterDefaults(false);
        $test = $this->form->getInputFilter();
        $this->assertSame($filter, $test);
        $this->assertSame($fieldsetFilter, $test->get('set'));
        $this->assertEquals(0, count($fieldsetFilter));
    }

    public function testCallingPrepareEnsuresInputFilterRetrievesDefaults()
    {
        $element = new TestAsset\ElementWithFilter('foo');
        $filter  = new InputFilter();
        $this->form->setInputFilter($filter);
        $this->form->add($element);
        $this->form->prepare();

        $this->assertTrue($filter->has('foo'));
        $input = $filter->get('foo');
        $filters = $input->getFilterChain();
        $this->assertEquals(1, count($filters));
        $validators = $input->getValidatorChain();
        $this->assertEquals(2, count($validators));
        $this->assertTrue($input->isRequired());
        $this->assertEquals('foo', $input->getName());

        // Issue #2586 Ensure default filters aren't added twice
        $filter = $this->form->getInputFilter();

        $this->assertTrue($filter->has('foo'));
        $input = $filter->get('foo');
        $filters = $input->getFilterChain();
        $this->assertEquals(1, count($filters));
        $validators = $input->getValidatorChain();
        $this->assertEquals(2, count($validators));
    }

    public function testCanProperlyPrepareNestedFieldsets()
    {
        $this->form->add([
            'name'       => 'foo',
            'attributes' => [
                'type'         => 'text'
            ]
        ]);

        $this->form->add([
            'type' => 'ZendTest\Form\TestAsset\BasicFieldset'
        ]);

        $this->form->prepare();

        $this->assertEquals('foo', $this->form->get('foo')->getName());

        $basicFieldset = $this->form->get('basic_fieldset');
        $this->assertEquals('basic_fieldset[field]', $basicFieldset->get('field')->getName());

        $nestedFieldset = $basicFieldset->get('nested_fieldset');
        $this->assertEquals(
            'basic_fieldset[nested_fieldset][anotherField]',
            $nestedFieldset->get('anotherField')->getName()
        );
    }

    public function testCanCorrectlyExtractDataFromComposedEntities()
    {
        $address = $this->getComposedEntity();

        $form = new TestAsset\CreateAddressForm();
        $form->bind($address);
        $form->setBindOnValidate(false);

        $this->assertEquals(true, $form->isValid());
        $this->assertEquals($address, $form->getData());
    }

    public function testCanCorrectlyPopulateDataToComposedEntities()
    {
        $address = $this->getComposedEntity();
        $emptyAddress = new TestAsset\Entity\Address();

        $form = new TestAsset\CreateAddressForm();
        $form->bind($emptyAddress);

        $data = [
            'address' => [
                'street' => '1 Rue des Champs Elysées',
                'city' => [
                    'name' => 'Paris',
                    'zipCode' => '75008',
                    'country' => [
                        'name' => 'France',
                        'continent' => 'Europe'
                    ]
                ]
            ]
        ];

        $form->setData($data);

        $this->assertEquals(true, $form->isValid());
        $this->assertEquals($address, $emptyAddress, var_export($address, 1) . "\n\n" . var_export($emptyAddress, 1));
    }

    public function testCanCorrectlyExtractDataFromOneToManyRelationship()
    {
        if (!extension_loaded('intl')) {
            // Required by \Zend\I18n\Validator\IsFloat
            $this->markTestSkipped('ext/intl not enabled');
        }

        $product = $this->getOneToManyEntity();

        $form = new TestAsset\NewProductForm();
        $form->bind($product);
        $form->setBindOnValidate(false);

        $this->assertEquals(true, $form->isValid());
        $this->assertEquals($product, $form->getData());
    }

    public function testCanCorrectlyPopulateDataToOneToManyEntites()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped("The Intl extension is not loaded");
        }
        $product = $this->getOneToManyEntity();
        $emptyProduct = new TestAsset\Entity\Product();

        $form = new TestAsset\NewProductForm();
        $form->bind($emptyProduct);

        $data = [
            'product' => [
                'name' => 'Chair',
                'price' => 10,
                'categories' => [
                    [
                        'name' => 'Office'
                    ],
                    [
                        'name' => 'Armchair'
                    ]
                ]
            ]
        ];

        $form->setData($data);

        $this->assertEquals(true, $form->isValid());
        $this->assertEquals($product, $emptyProduct, var_export($product, 1) . "\n\n" . var_export($emptyProduct, 1));
    }

    public function testCanCorrectlyPopulateOrphanedEntities()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped("The Intl extension is not loaded");
        }

        $form = new TestAsset\OrphansForm();

        $data = [
            'test' => [
                [
                    'name' => 'Foo'
                ],
                [
                    'name' => 'Bar'
                ],
            ]
        ];

        $form->setData($data);
        $valid = $form->isValid();
        $this->assertEquals(true, $valid);

        $formCollections = $form->getFieldsets();
        $formCollection = $formCollections['test'];

        $fieldsets = $formCollection->getFieldsets();

        $fieldsetFoo = $fieldsets[0];
        $fieldsetBar = $fieldsets[1];

        $objectFoo = $fieldsetFoo->getObject();
        $this->assertInstanceOf(
            'ZendTest\Form\TestAsset\Entity\Orphan',
            $objectFoo,
            'FormCollection with orphans does not bind objects from fieldsets'
        );

        $objectBar = $fieldsetBar->getObject();
        $this->assertInstanceOf(
            'ZendTest\Form\TestAsset\Entity\Orphan',
            $objectBar,
            'FormCollection with orphans does not bind objects from fieldsets'
        );

        $this->assertSame(
            'Foo',
            $objectFoo->name,
            'Object is not populated if it is an orphan in a fieldset inside a formCollection'
        );

        $this->assertSame(
            'Bar',
            $objectBar->name,
            'Object is not populated if it is an orphan in a fieldset inside a formCollection'
        );
    }

    public function testAssertElementsNamesAreNotWrappedAroundFormNameByDefault()
    {
        $form = new \ZendTest\Form\TestAsset\FormCollection();
        $form->prepare();

        $this->assertEquals('colors[0]', $form->get('colors')->get('0')->getName());
        $this->assertEquals('fieldsets[0][field]', $form->get('fieldsets')->get('0')->get('field')->getName());
    }

    public function testAssertElementsNamesCanBeWrappedAroundFormName()
    {
        $form = new \ZendTest\Form\TestAsset\FormCollection();
        $form->setWrapElements(true);
        $form->setName('foo');
        $form->prepare();

        $this->assertEquals('foo[colors][0]', $form->get('colors')->get('0')->getName());
        $this->assertEquals('foo[fieldsets][0][field]', $form->get('fieldsets')->get('0')->get('field')->getName());
    }

    public function testUnsetValuesNotBound()
    {
        $model = new stdClass;
        $validSet = [
            'bar' => 'always valid',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => 'always valid',
            ],
        ];
        $this->populateForm();
        $this->form->setHydrator(new Hydrator\ObjectProperty());
        $this->form->bind($model);
        $this->form->setData($validSet);
        $this->form->isValid();
        $data = $this->form->getData();
        $this->assertObjectNotHasAttribute('foo', $data);
        $this->assertObjectHasAttribute('bar', $data);
    }

    public function testRemoveCollectionFromValidationGroupWhenZeroCountAndNoData()
    {
        $dataWithoutCollection = [
            'foo' => 'bar'
        ];
        $this->populateForm();
        $this->form->add([
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'categories',
            'options' => [
                'count' => 0,
                'target_element' => [
                    'type' => 'ZendTest\Form\TestAsset\CategoryFieldset'
                ]
            ]
        ]);
        $this->form->setValidationGroup([
            'foo',
            'categories' => [
                'name'
            ]
        ]);
        $this->form->setData($dataWithoutCollection);
        $this->assertTrue($this->form->isValid());
    }

    public function testFieldsetValidationGroupStillPreparedWhenEmptyData()
    {
        $emptyData = [];

        $this->populateForm();
        $this->form->get('foobar')->add([
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'categories',
            'options' => [
                'count' => 0,
                'target_element' => [
                    'type' => 'ZendTest\Form\TestAsset\CategoryFieldset'
                ]
            ]
        ]);

        $this->form->setValidationGroup([
            'foobar' => [
                'categories' => [
                    'name'
                ]
            ]
        ]);

        $this->form->setData($emptyData);
        $this->assertFalse($this->form->isValid());
    }

    public function testApplyObjectInputFilterToBaseFieldsetAndApplyValidationGroup()
    {
        $fieldset = new Fieldset('foobar');
        $fieldset->add(new Element('foo'));
        $fieldset->setUseAsBaseFieldset(true);
        $this->form->add($fieldset);
        $this->form->setValidationGroup([
            'foobar'=> [
                'foo',
            ]
        ]);

        $inputFilterFactory = new InputFilterFactory();
        $inputFilter = $inputFilterFactory->createInputFilter([
            'foo' => [
                'name'       => 'foo',
                'required'   => true,
            ],
        ]);
        $model = new TestAsset\ValidatingModel();
        $model->setInputFilter($inputFilter);
        $this->form->bind($model);

        $this->form->setData([]);
        $this->assertFalse($this->form->isValid());

        $validSet = [
            'foobar' => [
                'foo' => 'abcde',
            ]
        ];
        $this->form->setData($validSet);
        $this->assertTrue($this->form->isValid());
    }

    public function testDoNotApplyEmptyInputFiltersToSubFieldsetOfCollectionElementsWithCollectionInputFilters()
    {
        $collectionFieldset = new Fieldset('item');
        $collectionFieldset->add(new Element('foo'));

        $collection = new Element\Collection('items');
        $collection->setCount(3);
        $collection->setTargetElement($collectionFieldset);
        $this->form->add($collection);

        $inputFilterFactory = new InputFilterFactory();
        $inputFilter = $inputFilterFactory->createInputFilter([
            'items' => [
                'type'         => 'Zend\InputFilter\CollectionInputFilter',
                'input_filter' => new InputFilter(),
            ],
        ]);

        $this->form->setInputFilter($inputFilter);

        $this->assertInstanceOf('Zend\InputFilter\CollectionInputFilter', $this->form->getInputFilter()->get('items'));
        $this->assertCount(1, $this->form->getInputFilter()->get('items')->getInputFilter()->getInputs());
    }

    public function testFormValidationCanHandleNonConsecutiveKeysOfCollectionInData()
    {
        $dataWithCollection = [
            'foo' => 'bar',
            'categories' => [
                0 => ['name' => 'cat1'],
                1 => ['name' => 'cat2'],
                3 => ['name' => 'cat3'],
            ],
        ];
        $this->populateForm();
        $this->form->add([
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'categories',
            'options' => [
                'count' => 1,
                'allow_add' => true,
                'target_element' => [
                    'type' => 'ZendTest\Form\TestAsset\CategoryFieldset'
                ]
            ]
        ]);
        $this->form->setValidationGroup([
            'foo',
            'categories' => [
                'name'
            ]
        ]);
        $this->form->setData($dataWithCollection);
        $this->assertTrue($this->form->isValid());
    }

    public function testAddNonBaseFieldsetObjectInputFilterToFormInputFilter()
    {
        $fieldset = new Fieldset('foobar');
        $fieldset->add(new Element('foo'));
        $fieldset->setUseAsBaseFieldset(false);
        $this->form->add($fieldset);

        $inputFilterFactory = new InputFilterFactory();
        $inputFilter = $inputFilterFactory->createInputFilter([
            'foo' => [
                'name'       => 'foo',
                'required'   => true,
            ],
        ]);
        $model = new TestAsset\ValidatingModel();
        $model->setInputFilter($inputFilter);

        $this->form->bind($model);

        $this->assertInstanceOf('Zend\InputFilter\InputFilterInterface', $this->form->getInputFilter()->get('foobar'));
    }

    public function testExtractDataHydratorStrategy()
    {
        $this->populateHydratorStrategyForm();

        $hydrator = new Hydrator\ObjectProperty();
        $hydrator->addStrategy('entities', new TestAsset\HydratorStrategy());
        $this->form->setHydrator($hydrator);

        $model = new TestAsset\HydratorStrategyEntityA();
        $this->form->bind($model);

        $validSet = [
            'entities' => [
                111,
                333
            ],
        ];

        $this->form->setData($validSet);
        $this->form->isValid();

        $data = $this->form->getData(Form::VALUES_AS_ARRAY);
        $this->assertEquals($validSet, $data);

        $entities = $model->getEntities();
        $this->assertCount(2, $entities);

        $this->assertEquals(111, $entities[0]->getField1());
        $this->assertEquals(333, $entities[1]->getField1());

        $this->assertEquals('AAA', $entities[0]->getField2());
        $this->assertEquals('CCC', $entities[1]->getField2());
    }

    public function testSetDataWithNullValues()
    {
        $this->populateForm();

        $set = [
            'foo' => null,
            'bar' => 'always valid',
            'foobar' => [
                'foo' => 'abcde',
                'bar' => 'always valid',
            ],
        ];
        $this->form->setData($set);
        $this->assertTrue($this->form->isValid());
    }

    public function testHydratorAppliedToBaseFieldset()
    {
        $fieldset = new Fieldset('foobar');
        $fieldset->add(new Element('foo'));
        $fieldset->setUseAsBaseFieldset(true);
        $this->form->add($fieldset);
        $this->form->setHydrator(new Hydrator\ArraySerializable());

        $baseHydrator = $this->form->get('foobar')->getHydrator();
        $this->assertInstanceOf('Zend\Stdlib\Hydrator\ArraySerializable', $baseHydrator);
    }

    public function testBindWithWrongFlagRaisesException()
    {
        $model = new stdClass;
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $this->form->bind($model, Form::VALUES_AS_ARRAY);
    }

    public function testSetBindOnValidateWrongFlagRaisesException()
    {
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $this->form->setBindOnValidate(Form::VALUES_AS_ARRAY);
    }

    public function testSetDataOnValidateWrongFlagRaisesException()
    {
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $this->form->setData(null);
    }

    public function testSetDataIsTraversable()
    {
        $this->form->setData(new \ArrayObject(['foo' => 'bar']));
        $this->assertTrue($this->form->isValid());
    }

    public function testResetPasswordValueIfFormIsNotValid()
    {
        $this->form->add([
            'type' => 'Zend\Form\Element\Password',
            'name' => 'password'
        ]);

        $this->form->add([
            'type' => 'Zend\Form\Element\Email',
            'name' => 'email'
        ]);

        $this->form->setData([
            'password' => 'azerty',
            'email'    => 'wrongEmail'
        ]);

        $this->assertFalse($this->form->isValid());
        $this->form->prepare();

        $this->assertEquals('', $this->form->get('password')->getValue());
    }

    public function testCorrectlyHydrateBaseFieldsetWhenHydratorThatDoesNotIgnoreInvalidDataIsUsed()
    {
        $fieldset = new Fieldset('example');
        $fieldset->add([
            'name' => 'foo'
        ]);

        // Add a hydrator that ignores if values does not exist in the
        $fieldset->setObject(new Entity\SimplePublicProperty());
        $fieldset->setHydrator(new \Zend\Stdlib\Hydrator\ObjectProperty());

        $this->form->add($fieldset);
        $this->form->setBaseFieldset($fieldset);
        $this->form->setHydrator(new \Zend\Stdlib\Hydrator\ObjectProperty());

        // Add some inputs that do not belong to the base fieldset
        $this->form->add([
            'type' => 'Zend\Form\Element\Submit',
            'name' => 'submit'
        ]);

        $object = new Entity\SimplePublicProperty();
        $this->form->bind($object);

        $this->form->setData([
            'submit' => 'Confirm',
            'example' => [
                'foo' => 'value example'
            ]
        ]);

        $this->assertTrue($this->form->isValid());

        // Make sure the object was not hydrated at the "form level"
        $this->assertFalse(isset($object->submit));
    }

    public function testPrepareBindDataAllowsFilterToConvertStringToArray()
    {
        $data = [
            'foo' => '1,2',
        ];

        $filteredData = [
            'foo' => [1, 2]
        ];

        $element = new TestAsset\ElementWithStringToArrayFilter('foo');
        $hydrator = $this->getMock('Zend\Stdlib\Hydrator\ArraySerializable');
        $hydrator->expects($this->any())->method('hydrate')->with($filteredData, $this->anything());

        $this->form->add($element);
        $this->form->setHydrator($hydrator);
        $this->form->setObject(new stdClass());
        $this->form->setData($data);
        $this->form->bindValues($data);
    }

    public function testGetValidationGroup()
    {
        $group = ['foo'];
        $this->form->setValidationGroup($group);
        $this->assertEquals($group, $this->form->getValidationGroup());
    }

    public function testGetValidationGroupReturnsNullWhenNoneSet()
    {
        $this->assertNull($this->form->getValidationGroup());
    }

    public function testPreserveEntitiesBoundToCollectionAfterValidation()
    {
        if (!extension_loaded('intl')) {
            // Required by \Zend\I18n\Validator\IsFloat
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->form->setInputFilter(new \Zend\InputFilter\InputFilter());
        $fieldset = new TestAsset\ProductCategoriesFieldset();
        $fieldset->setUseAsBaseFieldset(true);

        $product = new Entity\Product();
        $product->setName('Foobar');
        $product->setPrice(100);

        $c1 = new Entity\Category();
        $c1->setId(1);
        $c1->setName('First Category');

        $c2 = new Entity\Category();
        $c2->setId(2);
        $c2->setName('Second Category');

        $product->setCategories([$c1, $c2]);

        $this->form->add($fieldset);
        $this->form->bind($product);

        $data = [
            'product' => [
                'name' => 'Barbar',
                'price' => 200,
                'categories' => [
                    ['name' => 'Something else'],
                    ['name' => 'Totally different'],
                ],
            ],
        ];

        $hash1 = spl_object_hash($this->form->getObject()->getCategory(0));
        $this->form->setData($data);
        $this->form->isValid();
        $hash2 = spl_object_hash($this->form->getObject()->getCategory(0));

        // Returned object has to be the same as when binding or properties
        // will be lost. (For example entity IDs.)
        $this->assertEquals($hash1, $hash2);
    }

    public function testAddRemove()
    {
        $form = clone $this->form;
        $this->assertEquals($form, $this->form);

        $file = new Element\File('file_resource');
        $this->form->add($file);
        $this->assertTrue($this->form->has('file_resource'));
        $this->assertNotEquals($form, $this->form);

        $form->add($file)->remove('file_resource');
        $this->form->remove('file_resource');
        $this->assertEquals($form, $this->form);
    }

    public function testNestedFormElementNameWrapping()
    {
        //build form
        $rootForm = new Form;
        $leafForm = new Form('form');
        $element = new Element('element');
        $leafForm->setWrapElements(true);
        $leafForm->add($element);
        $rootForm->add($leafForm);

        //prepare for view
        $rootForm->prepare();
        $this->assertEquals('form[element]', $element->getName());
    }

    /**
     * @group 4996
     */
    public function testCanOverrideDefaultInputSettings()
    {
        $myFieldset = new TestAsset\MyFieldset();
        $myFieldset->setUseAsBaseFieldset(true);
        $form = new Form();
        $form->add($myFieldset);

        $inputFilter = $form->getInputFilter()->get('my-fieldset');
        $this->assertFalse($inputFilter->get('email')->isRequired());
    }

    /**
     * @group 5007
     */
    public function testComplexFormInputFilterMergesIntoExisting()
    {
        $this->form->setPreferFormInputFilter(true);
        $this->form->add([
            'name' => 'importance',
            'type'  => 'Zend\Form\Element\Select',
            'options' => [
                'label' => 'Importance',
                'empty_option' => '',
                'value_options' => [
                    'normal' => 'Normal',
                    'important' => 'Important'
                ],
            ],
        ]);

        $inputFilter = new \Zend\InputFilter\BaseInputFilter();
        $factory     = new \Zend\InputFilter\Factory();
        $inputFilter->add($factory->createInput([
            'name'     => 'importance',
            'required' => false,
        ]));

        $this->assertTrue($this->form->getInputFilter()->get('importance')->isRequired());
        $this->assertFalse($inputFilter->get('importance')->isRequired());
        $this->form->getInputFilter();
        $this->form->setInputFilter($inputFilter);
        $this->assertFalse($this->form->getInputFilter()->get('importance')->isRequired());
    }

    /**
     * @group 5007
     */
    public function testInputFilterOrderOfPrecedence1()
    {
        $spec = [
            'name' => 'test',
            'elements' => [
                [
                    'spec' => [
                        'name' => 'element',
                        'type' => 'Zend\Form\Element\Checkbox',
                        'options' => [
                            'use_hidden_element' => true,
                            'checked_value' => '1',
                            'unchecked_value' => '0'
                        ]
                    ]
                ]
            ],
            'input_filter' => [
                'element' => [
                    'required' => false,
                    'filters' => [
                        [
                            'name' => 'Boolean'
                        ]
                    ],
                    'validators' => [
                        [
                            'name' => 'InArray',
                            'options' => [
                                'haystack' => [
                                    "0",
                                    "1"
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $factory = new Factory();
        $this->form = $factory->createForm($spec);
        $this->form->setPreferFormInputFilter(true);
        $this->assertFalse(
            $this->form->getInputFilter()->get('element')->isRequired()
        );
    }

    /**
     * @group 5015
     */
    public function testCanSetPreferFormInputFilterFlagViaSetOptions()
    {
        $flag = ! $this->form->getPreferFormInputFilter();
        $this->form->setOptions([
            'prefer_form_input_filter' => $flag,
        ]);
        $this->assertSame($flag, $this->form->getPreferFormInputFilter());
    }

    /**
     * @group 5015
     */
    public function testFactoryCanSetPreferFormInputFilterFlag()
    {
        $factory = new Factory();
        foreach ([true, false] as $flag) {
            $form = $factory->createForm([
                'name'    => 'form',
                'options' => [
                    'prefer_form_input_filter' => $flag,
                ],
            ]);
            $this->assertSame($flag, $form->getPreferFormInputFilter());
        }
    }

    /**
     * @group 5028
     */
    public function testPreferFormInputFilterFlagIsEnabledByDefault()
    {
        $this->assertTrue($this->form->getPreferFormInputFilter());
    }

    /**
     * @group 5050
     */
    public function testFileInputFilterNotOverwritten()
    {
        $form = new TestAsset\FileInputFilterProviderForm();

        $formInputFilter     = $form->getInputFilter();
        $fieldsetInputFilter = $formInputFilter->get('file_fieldset');
        $fileInput           = $fieldsetInputFilter->get('file_field');

        $this->assertInstanceOf('Zend\InputFilter\FileInput', $fileInput);

        $chain = $fileInput->getFilterChain();
        $this->assertCount(1, $chain, var_export($chain, 1));
    }

    public function testInputFilterNotAddedTwiceWhenUsingFieldsets()
    {
        $form = new Form();

        $fieldset = new TestAsset\FieldsetWithInputFilter('fieldset');
        $form->add($fieldset);
        $filters = $form->getInputFilter()->get('fieldset')->get('foo')->getFilterChain();
        $this->assertEquals(1, $filters->count());
    }

    public function testFormWithNestedCollections()
    {
        $spec = [
            'name' => 'test',
            'elements' => [
                [
                    'spec' => [
                        'name' => 'name',
                        'type' => 'Zend\Form\Element\Text',
                    ],
                    'spec' => [
                        'name' => 'groups',
                        'type' => 'Zend\Form\Element\Collection',
                        'options' => [
                            'target_element' => [
                                'type' => 'Zend\Form\Fieldset',
                                'name' => 'group',
                                'elements' => [
                                    [
                                        'spec' => [
                                            'type' => 'Zend\Form\Element\Text',
                                            'name' => 'group_class',
                                        ],
                                    ],
                                    [
                                        'spec' => [
                                            'type' => 'Zend\Form\Element\Collection',
                                            'name' => 'items',
                                            'options' => [
                                                'target_element' => [
                                                    'type' => 'Zend\Form\Fieldset',
                                                    'name' => 'item',
                                                    'elements' => [
                                                        [
                                                            'spec' => [
                                                                'type' => 'Zend\Form\Element\Text',
                                                                'name' => 'id',
                                                            ],
                                                        ],
                                                        [
                                                            'spec' => [
                                                                'type' => 'Zend\Form\Element\Text',
                                                                'name' => 'type',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ]
                ]
            ],
            'input_filter' => [
                'type' => 'Zend\InputFilter\InputFilter',
                'name' => [
                    'filters' => [
                        ['name' => 'StringTrim'],
                        ['name' => 'Null'],
                    ],
                    'validators' => [
                        [
                            'name' => 'StringLength',
                            'options' => [
                                'max' => 255,
                            ],
                        ],
                    ],
                ],
                'groups' => [
                    'type' => 'Zend\InputFilter\CollectionInputFilter',
                    'input_filter' => [
                        'type' => 'Zend\InputFilter\InputFilter',
                        'group_class' => [
                            'required' => false,
                        ],
                        'items' => [
                            'type' => 'Zend\InputFilter\CollectionInputFilter',
                            'input_filter' => [
                                'type' => 'Zend\InputFilter\InputFilter',
                                'id' => [
                                    'required' => false,
                                ],
                                'type' => [
                                    'required' => false,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $factory = new Factory();
        $this->form = $factory->createForm($spec);

        $data = [
            'name' => 'foo',
            'groups' => [
                [
                    'group_class' => 'bar',
                    'items' => [
                        [
                            'id' => 100,
                            'type' => 'item-1',
                        ],
                    ],
                ],
                [
                    'group_class' => 'bar',
                    'items' => [
                        [
                            'id' => 200,
                            'type' => 'item-2',
                        ],
                        [
                            'id' => 300,
                            'type' => 'item-3',
                        ],
                        [
                            'id' => 400,
                            'type' => 'item-4',
                        ],
                    ],
                ],
                [
                    'group_class' => 'biz',
                    'items' => [],
                ],
            ],
        ];

        $this->form->setData($data);

        $isValid = $this->form->isValid();
        $this->assertEquals($data, $this->form->getData());
    }

    public function testFormWithCollectionsAndNestedFieldsetsWithInputFilterProviderInterface()
    {
        $this->form->add([
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'nested_fieldset_with_input_filter_provider',
            'options' => [
                'label' => 'InputFilterProviderFieldset',
                'count' => 1,
                'target_element' => [
                    'type' => 'ZendTest\Form\TestAsset\InputFilterProviderFieldset'
                ]
            ],
        ]);

        $this->assertTrue(
            $this->form->getInputFilter()
                ->get('nested_fieldset_with_input_filter_provider')
                ->getInputFilter()
                ->get('foo')
            instanceof \Zend\InputFilter\Input
        );
    }

    public function testFormElementValidatorsMergeIntoAppliedInputFilter()
    {
        $this->form->add([
            'name' => 'importance',
            'type'  => 'Zend\Form\Element\Select',
            'options' => [
                'label' => 'Importance',
                'empty_option' => '',
                'value_options' => [
                    'normal' => 'Normal',
                    'important' => 'Important'
                ],
            ],
        ]);

        $inputFilter = new BaseInputFilter();
        $factory     = new InputFilterFactory();
        $inputFilter->add($factory->createInput([
            'name'     => 'importance',
            'required' => false,
        ]));

        $data = [
            'importance' => 'unimporant'
        ];

        $this->form->setInputFilter($inputFilter);
        $this->form->setData($data);
        $this->assertFalse($this->form->isValid());

        $data = [];

        $this->form->setData($data);
        $this->assertTrue($this->form->isValid());
    }

    /**
     * @param bool $expectedIsValid
     * @param array $expectedFormData
     * @param array $data
     * @param string $unselectedValue
     * @param bool $useHiddenElement
     * @dataProvider formWithSelectMultipleAndEmptyUnselectedValueDataProvider
     */
    public function testFormWithSelectMultipleAndEmptyUnselectedValue(
        $expectedIsValid,
        array $expectedFormData,
        array $data,
        $unselectedValue,
        $useHiddenElement
    ) {
        $this->form->add([
            'name' => 'multipleSelect',
            'type'  => 'Zend\Form\Element\Select',
            'attributes' => ['multiple' => 'multiple'],
            'options' => [
                'label' => 'Importance',
                'use_hidden_element' => $useHiddenElement,
                'unselected_value' => $unselectedValue,
                'value_options' => [
                    'foo' => 'Foo',
                    'bar' => 'Bar'
                ],
            ],
        ]);

        $actualIsValid = $this->form->setData($data)->isValid();
        $this->assertEquals($expectedIsValid, $actualIsValid);

        $formData = $this->form->getData();
        $this->assertEquals($expectedFormData, $formData);
    }

    /**
     * @return array
     */
    public function formWithSelectMultipleAndEmptyUnselectedValueDataProvider()
    {
        return [
            [
                true,
                ['multipleSelect' => ['foo']],
                ['multipleSelect' => ['foo']],
                '',
                true
            ],
            [
                true,
                ['multipleSelect' => []],
                ['multipleSelect' => ''],
                '',
                true
            ],
            [
                true,
                ['multipleSelect' => []],
                ['multipleSelect' => 'empty'],
                'empty',
                true
            ],
            [
                false,
                ['multipleSelect' => ''],
                ['multipleSelect' => ''],
                'empty',
                true
            ],
            [
                false,
                ['multipleSelect' => ''],
                ['multipleSelect' => ''],
                '',
                false
            ],
            [
                true,
                ['multipleSelect' => []],
                ['multipleSelect' => 'foo'],
                'foo',
                true
            ],
        ];
    }

    public function testCanSetUseInputFilterDefaultsViaArray()
    {
        $spec = [
            'name' => 'test',
            'options' => [
                'use_input_filter_defaults' => false
            ]
        ];

        $factory = new Factory();
        $this->form = $factory->createForm($spec);
        $this->assertFalse($this->form->useInputFilterDefaults());
    }



    /**
     * Error test for https://github.com/zendframework/zf2/issues/6363 comment #1
     */
    public function testSetValidationGroupOnFormWithNestedCollectionsRaisesInvalidArgumentException()
    {
        $this->form = new TestAsset\NestedCollectionsForm;

        $data = [
            'testFieldset' => [
                'groups' => [
                    [
                        'name' => 'first',
                        'items' => [
                            [
                                'itemId' => 1,
                            ],
                            [
                                'itemId' => 2,
                            ],
                        ],
                    ],
                    [
                        'name' => 'second',
                        'items' => [
                            [
                                'itemId' => 3,
                            ],
                        ],
                    ],
                    [
                        'name' => 'third',
                        'items' => [],
                    ],
                ],
            ],
        ];

        $this->form->setData($data);
        $this->form->isValid();

        $this->assertEquals($data, $this->form->getData());
    }


    /**
     * Test for https://github.com/zendframework/zf2/issues/6363 comment #2
     */
    public function testSetValidationGroupOnFormWithNestedCollectionsPopulatesOnlyFirstNestedCollectionElement()
    {
        $this->form = new TestAsset\NestedCollectionsForm;

        $data = [
            'testFieldset' => [
                'groups' => [
                    [
                        'name' => 'first',
                        'items' => [
                            [
                                'itemId' => 1,
                            ],
                            [
                                'itemId' => 2,
                            ],
                        ],
                    ],
                    [
                        'name' => 'second',
                        'items' => [
                            [
                                'itemId' => 3,
                            ],
                            [
                                'itemId' => 4,
                            ],
                        ],
                    ],
                    [
                        'name' => 'third',
                        'items' => [
                            [
                                'itemId' => 5,
                            ],
                            [
                                'itemId' => 6,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->form->setData($data);
        $this->form->isValid();

        $this->assertEquals($data, $this->form->getData());
    }
}
