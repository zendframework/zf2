<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\TestAsset;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class FieldsetWithInputFilter extends Fieldset implements InputFilterProviderInterface
{
    public function getInputFilterSpecification()
    {
        return [
            'foo' => [
                'required' => true,
                'filters' => [
                    ['name' => 'Zend\Filter\StringTrim'],
                ],
                'validators' => [
                    ['name' => 'Zend\Validator\NotEmpty'],
                    ['name' => 'Zend\I18n\Validator\Alnum'],
                ],
            ],
            'bar' => [
                'required' => false,
                'filters' => [
                    ['name' => 'Zend\Filter\StringTrim'],
                ],
            ],
        ];
    }
}
