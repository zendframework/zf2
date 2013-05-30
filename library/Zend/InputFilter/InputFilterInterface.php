<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

/**
 * Input filter interface
 */
interface InputFilterInterface
{
    /**
     * Add an input or another input filter (if no name was set, it will extract the one set in
     * the input or input filter)
     *
     * @param  InputInterface|InputFilterInterface $inputOrInputFilter
     * @param  string|null                         $name
     * @return void
     */
    public function add($inputOrInputFilter, $name = null);

    /**
     * Get an input or an input filter by name
     *
     * @param  string $name
     * @return InputInterface|InputFilterInterface
     */
    public function get($name);

    /**
     * Check if the input filter contains an input or another input filter with the name given
     *
     * @param  string $name
     * @return bool
     */
    public function has($name);

    /**
     * Remove the input or input filter from the given name
     *
     * @param  string $name
     * @return void
     */
    public function remove($name);

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
     * Set the data that need to be validated
     *
     * @param  array $data
     * @return void
     */
    public function setData(array $data);

    /**
     * Get the filtered value
     *
     * @param  string $name
     * @return mixed
     */
    public function getValue($name);

    /**
     * Get the unfiltered value
     *
     * @param  string $name
     * @return mixed
     */
    public function getRawValue($name);

    /**
     * Get the filtered values
     *
     * @return array
     */
    public function getValues();

    /**
     * Get the unfiltered values
     *
     * @return array
     */
    public function getRawValues();

    /**
     * Set the validation group
     *
     * @param  array $validationGroup
     * @return void
     */
    public function setValidationGroup(array $validationGroup);

    /**
     * Get the validation group
     *
     * @return array
     */
    public function getValidationGroup();

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
