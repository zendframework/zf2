<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

use Zend\Filter\FilterChain;
use Zend\Validator\ValidatorChain;

/**
 * Input interface
 */
interface InputInterface
{
    /**
     * Constants
     */
    const REQUIRED_VALIDATOR_PRIORITY = 1000;

    /**
     * Set the name of the input filter
     *
     * @param  string $name
     * @return void
     */
    public function setName($name);

    /**
     * Get the name of the input filter
     *
     * @return string
     */
    public function getName();

    /**
     * Set the fallback value
     *
     * @param  mixed $fallbackValue
     * @return void
     */
    public function setFallbackValue($fallbackValue);

    /**
     * Get the fallback value
     *
     * @return mixed
     */
    public function getFallbackValue();

    /**
     * Set if the input is required. This is a shortcut of manually adding a NotEmpty validator with
     * a very high priority into the validator chain
     *
     * @param  bool $required
     * @return void
     */
    public function setRequired($required);

    /**
     * Get if the input is required
     *
     * @return bool
     */
    public function isRequired();

    /**
     * Set if the input is allowed to be empty
     *
     * @param  bool $allowEmpty
     * @return void
     */
    public function setAllowEmpty($allowEmpty);

    /**
     * Get if the input is allowed to be empty
     *
     * @return bool
     */
    public function allowEmpty();

    /**
     * Set if the validation should break if one validator fails
     *
     * @param  bool $breakOnFailure
     * @return void
     */
    public function setBreakOnFailure($breakOnFailure);

    /**
     * If set to true, then no other inputs are validated
     *
     * @return bool
     */
    public function breakOnFailure();

    /**
     * Set the filter chain
     *
     * @param  FilterChain $filterChain
     * @return void
     */
    public function setFilterChain(FilterChain $filterChain);

    /**
     * Get the filter chain
     *
     * @return FilterChain
     */
    public function getFilterChain();

    /**
     * Set the validator chain
     *
     * @param  ValidatorChain $validatorChain
     * @return void
     */
    public function setValidatorChain(ValidatorChain $validatorChain);

    /**
     * Get the validator chain
     *
     * @return ValidatorChain
     */
    public function getValidatorChain();

    /**
     * Validate the value, and return the error messages (if any)
     *
     * @param  mixed      &$value   Value to validate
     * @param  mixed|null $context An optional context used for validation
     * @return array
     */
    public function validate(&$value, $context = null);
}
