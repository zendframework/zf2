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
     * Set the data of the input
     *
     * @param  $data
     * @return void
     */
    public function setData($data);

    /**
     * Get the filtered value
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Get the unfiltered value
     *
     * @return mixed
     */
    public function getRawValue();

    /**
     * Set if the validation should break if one validator fails
     *
     * @param  bool $breakOnFailure
     * @return void
     */
    public function setBreakOnFailure($breakOnFailure);

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
     * If set to true, then no other inputs are validated
     *
     * @return bool
     */
    public function breakOnFailure();

    /**
     * Get the filter chain
     *
     * @return FilterChain
     */
    public function getFilterChain();

    /**
     * Get the validator chain
     *
     * @return ValidatorChain
     */
    public function getValidatorChain();

    /**
     * Check if the input filter is valid
     *
     * @param  mixed|null $context An optional context used for validation
     * @return bool
     */
    public function isValid($context = null);

    /**
     * Get the error messages that may have occurred during validation (if any)
     *
     * @return array
     */
    public function getErrorMessages();
}
