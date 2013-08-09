<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

use RecursiveIterator;

/**
 * Input collection interface
 */
interface InputCollectionInterface extends RecursiveIterator
{
    /**
     * Add an input or another input collection (if no name was set, it will extract the one set in
     * the input or input collection)
     *
     * @param  InputInterface|InputCollectionInterface $inputOrInputCollection
     * @param  string|null                             $name
     * @return void
     */
    public function add($inputOrInputCollection, $name = null);

    /**
     * Get an input or an input collection by name
     *
     * @param  string $name
     * @return InputInterface|InputCollectionInterface
     */
    public function get($name);

    /**
     * Check if the input collection contains an input or another input collection with the name given
     *
     * @param  string $name
     * @return bool
     */
    public function has($name);

    /**
     * Remove the input or input collection from the given name
     *
     * @param  string $name
     * @return void
     */
    public function remove($name);

    /**
     * Set the name of the input collection
     *
     * @param  string $name
     * @return void
     */
    public function setName($name);

    /**
     * Get the name of the input collection
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
     * Get the valid inputs
     *
     * @return InputInterface[]
     */
    public function getValidInputs();

    /**
     * Get the invalid inputs
     *
     * @return InputInterface[]
     */
    public function getInvalidInputs();

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
     * Set error messages for a given input
     *
     * @param  string $name
     * @param  array $errorMessages
     * @return void
     */
    public function setErrorMessages($name, array $errorMessages);

    /**
     * Get the error messages that may have occurred during validation (if any)
     *
     * @return array
     */
    public function getErrorMessages();

    /**
     * Check if the input filter is valid
     *
     * @param  mixed|null $context An optional context used for validation
     * @return bool
     */
    public function isValid($context = null);
}
