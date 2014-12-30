<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form\Element;

use Zend\Filter\FilterInterface;
use Zend\Filter\StringTrim;
use Zend\Form\Element;
use Zend\I18n\Filter\NumberParse;
use Zend\InputFilter\InputProviderInterface;
use Zend\Validator\GreaterThan as GreaterThanValidator;
use Zend\Validator\LessThan as LessThanValidator;
use Zend\Validator\Regex as RegexValidator;
use Zend\Validator\Step as StepValidator;
use Zend\Validator\ValidatorInterface;

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
     * @var array|null
     */
    protected $validators;

    /**
     * @var array|null
     */
    protected $filters;

    /**
     * Get validator
     *
     * @return ValidatorInterface[]
     */
    protected function getValidators()
    {
        if ($this->validators) {
            return $this->validators;
        }

        // HTML5 always transmits values in the format "1000.01", without a
        // thousand separator. The prior use of the i18n Float validator
        // allowed the thousand separator, which resulted in wrong numbers
        // when casting to float.
        $this->validators[] = new RegexValidator('(^-?\d*(\.\d+)?$)');

        $inclusive = true;
        if (isset($this->attributes['inclusive'])) {
            $inclusive = $this->attributes['inclusive'];
        }

        if (isset($this->attributes['min'])) {
            $validators[] = new GreaterThanValidator(array(
                'min' => $this->attributes['min'],
                'inclusive' => $inclusive
            ));
        }
        if (isset($this->attributes['max'])) {
            $this->validators[] = new LessThanValidator(array(
                'max' => $this->attributes['max'],
                'inclusive' => $inclusive
            ));
        }

        if ( ! isset($this->attributes['step'])
            || 'any' !== $this->attributes['step']
        ) {
            $this->validators[] = new StepValidator(array(
                'baseValue' => (isset($this->attributes['min'])) ? $this->attributes['min'] : 0,
                'step' => (isset($this->attributes['step'])) ? $this->attributes['step'] : 1,
            ));
        }

        return $this->validators;
    }

    /**
     * Get Filter Specification
     *
     * @return FilterInterface[]
     */
    protected function getFilters()
    {
        if ($this->filters) {
            return $this->filters;
        }

        $this->filters = array(
            new StringTrim()
        );

        if (isset($this->options['format'])) {
            $this->filters[] = new NumberParse(array(
                'locale' => 'en',
                'type' => $this->options['format']
            ));
        }

        return $this->filters;
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
            'filters' => $this->getFilters(),
            'validators' => $this->getValidators(),
        );
    }
}
