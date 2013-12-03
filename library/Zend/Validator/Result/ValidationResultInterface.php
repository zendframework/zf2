<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator\Result;

use JsonSerializable;
use Serializable;

/**
 * A validation result is an object that holds the result of the validation against
 * a single validator, and contains all the error messages that may have been
 * generated.
 */
interface ValidationResultInterface extends Serializable, JsonSerializable
{
    /**
     * Merge one validation result with another
     *
     * @param  ValidationResultInterface $validationResult
     * @return void
     */
    public function merge(ValidationResultInterface $validationResult);

    /**
     * Is the validation result valid?
     *
     * @return bool
     */
    public function isValid();

    /**
     * Get the data
     *
     * @return mixed
     */
    public function getData();

    /**
     * Get the raw error messages (in those messages, variables are not injected, useful for translation)
     *
     * @return array
     */
    public function getRawErrorMessages();

    /**
     * Get the error messages (with the variables replaced) associated with the validation result
     *
     * @return array
     */
    public function getErrorMessages();

    /**
     * Get the optional message variables that get injected into error messages
     *
     * @return array
     */
    public function getMessageVariables();
}
