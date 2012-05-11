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
 * @package    Zend_InputFilter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\InputFilter;

use ArrayAccess;
use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_InputFilter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class InputFilter implements InputFilterInterface
{
    protected $data;
    protected $inputs = array();

    /**
     * Countable: number of inputs in this input filter
     *
     * Only details the number of direct children.
     * 
     * @return int
     */
    public function count()
    {
        return count($this->inputs);
    }

    /**
     * Add an input to the input filter
     * 
     * @param  InputInterface|InputFilterInterface $input 
     * @param  null|string $name Name used to retrieve this input
     * @return InputFilterInterface
     */
    public function add($input, $name = null)
    {
        if (!$input instanceof InputInterface && !$input instanceof InputFilterInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an instance of %s or %s as its first argument; received "%s"',
                __METHOD__,
                'Zend\InputFilter\InputInterface',
                'Zend\InputFilter\InputFilterInterface',
                (is_object($input) ? get_class($input) : gettype($input))
            ));
        }

        if ($name === null) {
            $name = $input->getName();
        }
        $this->inputs[$name] = $input;
        return $this;
    }

    /**
     * Retrieve a named input
     * 
     * @param  string $name 
     * @return InputInterface|InputFilterInterface
     */
    public function get($name)
    {
        if (!array_key_exists($name, $this->inputs)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: no input found matching "%s"',
                __METHOD__,
                $name
            ));
        }
        return $this->inputs[$name];
    }

    /**
     * Set data to use when validating and filtering
     * 
     * @param  array|Traversable $data 
     * @return InputFilterInterface
     */
    public function setData($data)
    {
        if (!is_array($data) && !$data instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable argument; received %s',
                __METHOD__,
                (is_object($data) ? get_class($data) : gettype($data))
            ));
        }
        if (is_object($data) && !$data instanceof ArrayAccess) {
            $data = ArrayUtils::iteratorToArray($data);
        }
        $this->data = $data;
        return $this;
    }

    /**
     * Is the data set valid?
     * 
     * @return bool
     */
    public function isValid()
    {
        if (null === $this->data) {
            throw new Exception\RuntimeException(sprintf(
                '%s: no data present to validate!',
                __METHOD__
            ));
        }

        $valid = true;
        foreach ($this->inputs as $name => $input) {
            if (!isset($this->data[$name])) {
                // Not sure how to handle input filters in this case
                if ($input instanceof InputFilterInterface) {
                    $input->setData(array());
                    if (!$input->isValid()) {
                        $valid = false;
                    }
                    continue;
                }

                // no matching value in data
                // - test if input is required
                // - test if input allows empty
                if (!$input->isRequired()) {
                    continue;
                }

                if ($input->allowEmpty()) {
                    continue;
                }

                // How do we mark the input as invalid in this case?

                // Mark validation as having failed
                $valid = false;
                if ($input->breakOnFailure()) {
                    // We failed validation, and this input is marked to
                    // break on failure
                    return false;
                }
                continue;
            }

            $value = $this->data[$name];
            if ($input instanceof InputFilterInterface) {
                $input->setData($value);
                if (!$input->isValid()) {
                    $valid = false;
                }
            }
            if ($input instanceof InputInterface) {
                $input->setValue($value);
                if (!$input->isValid($this->data)) {
                    // Validation failure
                    $valid = false;

                    if ($input->breakOnFailure()) {
                        return false;
                    }
                }
            }
        }
    }

    /**
     * Provide a list of one or more elements indicating the complete set to validate
     *
     * When provided, calls to {@link isValid()} will only validate the provided set.
     *
     * If the initial value is {@link VALIDATE_ALL}, the current validation group, if
     * any, should be cleared.
     *
     * Implementations should allow passing a single array value, or multiple arguments,
     * each specifying a single input.
     * 
     * @param  mixed $name 
     * @return InputFilterInterface
     */
    public function setValidationGroup($name)
    {
    }

    /**
     * Return a list of inputs that were invalid.
     *
     * Implementations should return an associative array of name/input pairs
     * that failed validation.
     * 
     * @return InputInterface[]
     */
    public function getInvalidInput()
    {
    }

    /**
     * Return a list of inputs that were valid.
     *
     * Implementations should return an associative array of name/input pairs
     * that passed validation.
     * 
     * @return InputInterface[]
     */
    public function getValidInput()
    {
    }

    /**
     * Return a list of filtered values
     *
     * List should be an associative array, with the values filtered. If
     * validation failed, this should raise an exception.
     * 
     * @return array
     */
    public function getValues()
    {
    }

    /**
     * Return a list of unfiltered values
     *
     * List should be an associative array of named input/value pairs,
     * with the values unfiltered.
     * 
     * @return array
     */
    public function getRawValues()
    {
    }

    /**
     * Return a list of validation failure messages
     *
     * Should return an associative array of named input/message list pairs.
     * Pairs should only be returned for inputs that failed validation.
     * 
     * @return array
     */
    public function getMessages()
    {
    }
}
