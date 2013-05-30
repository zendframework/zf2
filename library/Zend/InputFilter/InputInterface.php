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
     * @return bool
     */
    public function isValid();

    /**
     * Get the error messages that may have occurred during validation (if any)
     *
     * @return array
     */
    public function getErrorMessages();
}
