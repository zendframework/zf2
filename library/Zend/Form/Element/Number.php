<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form\Element;

use Zend\Form\Element;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\Float as NumberValidator;
use Zend\Validator\GreaterThan as GreaterThanValidator;
use Zend\Validator\LessThan as LessThanValidator;
use Zend\Validator\Step as StepValidator;
use Zend\Validator\ValidatorInterface;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Number extends Element implements InputProviderInterface
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'number',
    );

    /**
     * @var array
     */
    protected $validators;

    /**
     * Add a validator
     *
     * @param  ValidatorInterface $validator
     * @return Number
     */
    public function addValidator(ValidatorInterface $validator)
    {
        $this->validators[] = $validator;
        return $this;
    }

    /**
     * Set an array of validators
     *
     * @param array $validators
     * @return Number
     */
    public function setValidators(array $validators)
    {
        foreach ($validators as $validator) {
            $this->addValidator($validator);
        }
        return $this;
    }

    /**
     * Get validator
     *
     * @return ValidatorInterface
     */
    public function getValidators()
    {
        if (null === $this->validators) {
            $this->addValidator(new NumberValidator());

            $inclusive = true;
            if (!empty($this->attributes['inclusive'])) {
                $inclusive = $this->attributes['inclusive'];
            }

            if (isset($this->attributes['min'])) {
                $this->addValidator(new GreaterThanValidator(array(
                    'min' => $this->attributes['min'],
                    'inclusive' => $inclusive
                )));
            }
            if (isset($this->attributes['max'])) {
                $this->addValidator(new LessThanValidator(array(
                    'max' => $this->attributes['max'],
                    'inclusive' => $inclusive
                )));
            }

            if (isset($this->attributes['step']) && $this->attributes['step'] !== 'any') {
                $this->addValidator(new StepValidator(array(
                    'baseValue' => (isset($this->attributes['min'])) ? $this->attributes['min'] : 0,
                    'step' => $this->attributes['step']
                )));
            }
        }

        return $this->validators;
    }

    /**
     * Provide default input rules for this element
     *
     * Attaches a number validator, as well as a greater than and less than validators
     *
     * @return array
     */
    public function getInputSpecification()
    {
        return array(
            'name' => $this->getName(),
            'required' => true,
            'filters' => array(
                array('name' => 'Zend\Filter\StringTrim')
            ),
            'validators' => $this->getValidators(),
        );
    }
}
