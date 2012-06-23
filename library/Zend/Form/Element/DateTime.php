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

use Zend\Date\Date as ZendDate;
use Zend\Form\Element;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\Date as DateValidator;
use Zend\Validator\GreaterThan as GreaterThanValidator;
use Zend\Validator\LessThan as LessThanValidator;
use Zend\Validator\DateStep as DateStepValidator;
use Zend\Validator\ValidatorInterface;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DateTime extends Element implements InputProviderInterface
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'datetime',
    );

    /**
     * @var array
     */
    protected $validators;

    /**
     * Set validator
     *
     * @param  array $validator
     * @return DateTime
     */
    public function setValidators($validators)
    {
        $this->validators = $validators;
        return $this;
    }

    /**
     * Get validators
     *
     * @return array
     */
    public function getValidators()
    {
        if (null === $this->validator) {
            $validators = array();

            $validators[] = new DateValidator(array(
                'format' => ZendDate::ISO_8601
            ));
            if (isset($this->attributes['min'])) {
                $validators[] = new GreaterThanValidator(array(
                    'min'       => $this->attributes['min'],
                    'inclusive' => true,
                ));
            }
            if (isset($this->attributes['max'])) {
                $validators[] = new LessThanValidator(array(
                    'max'       => $this->attributes['max'],
                    'inclusive' => true,
                ));
            }
            if (isset($this->attributes['step']) && 'any' !== $this->attributes['step']) {
                $validators[] = $this->getStepValidator();
            }

            $this->setValidators($validators);
        }
        return $this->validator;
    }

    /**
     * Retrieves a DateStep Validator configured for a DateTime Input type
     *
     * @return DateStepValidator
     */
    protected function getStepValidator()
    {
        $stepValue = (isset($this->attributes['step']))
                     ? $this->attributes['step'] : 1; // Minutes

        $baseValue = (isset($this->attributes['min']))
                     ? $this->attributes['min'] : '1970-01-01T00:00:00';

        return new DateStepValidator(array(
            'format'       => ZendDate::ISO_8601,
            'baseValue'    => $baseValue,
            'stepValue'    => $stepValue,
            'stepDatePart' => ZendDate::MINUTE,
        ));
    }

    /**
     * Provide default input rules for this element
     *
     * Attaches default validators for the datetime input.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        return array(
            'name' => $this->getName(),
            'required' => true,
            'filters' => array(
                array('name' => 'Zend\Filter\StringTrim'),
            ),
            'validators' => $this->getValidators(),
        );
    }
}
