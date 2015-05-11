<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\TestAsset;

use ZendTest\Form\TestAsset\Entity\Product;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class ProductFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('product');
        $this->setHydrator(new ClassMethodsHydrator())
             ->setObject(new Product());

        $this->add([
            'name' => 'name',
            'options' => [
                'label' => 'Name of the product'
            ],
            'attributes' => [
                'required' => 'required'
            ]
        ]);

        $this->add([
            'name' => 'price',
            'options' => [
                'label' => 'Price of the product'
            ],
            'attributes' => [
                'required' => 'required'
            ]
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'categories',
            'options' => [
                'label' => 'Please choose categories for this product',
                'count' => 2,
                'target_element' => [
                    'type' => 'ZendTest\Form\TestAsset\CategoryFieldset'
                ]
            ]
        ]);

        $this->add([
            'type' => 'ZendTest\Form\TestAsset\CountryFieldset',
            'name' => 'made_in_country',
            'object' => 'ZendTest\Form\TestAsset\Entity\Country',
            'hydrator' => 'Zend\Stdlib\Hydrator\ClassMethods',
            'options' => [
                'label' => 'Please choose the country',
            ]
        ]);
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'name' => [
                'required' => true,
            ],
            'price' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'IsFloat'
                    ]
                ]
            ],
            'made_in_country' => [
                'required' => false,
            ],
        ];
    }
}
