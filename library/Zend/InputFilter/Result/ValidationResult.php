<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter\Result;

/**
 * Validation result class
 */
class ValidationResult implements ValidationResultInterface
{
    /**
     * @var array
     */
    protected $rawValues = array();

    /**
     * @var array
     */
    protected $values = array();

    /**
     * @var array
     */
    protected $errorMessages = array();

    /**
     * @param array $rawValues
     * @param array $values
     * @param array $errorMessages
     */
    public function __construct($rawValues, $values, array $errorMessages = array())
    {
        $this->rawValues     = $rawValues;
        $this->values        = $values;
        $this->errorMessages = $errorMessages;
    }

    /**
     * {@inheritDoc}
     */
    public function isValid()
    {
        return empty($this->errorMessages);
    }

    /**
     * {@inheritDoc}
     */
    public function getValues()
    {
        $this->values;
    }

    /**
     * {@inheritDoc}
     */
    public function getRawValues()
    {
        $this->rawValues;
    }

    /**
     * {@inheritDoc}
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * Serialize the error messages
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->errorMessages);
    }

    /**
     * Unserialize the error messages
     *
     * @param  string $serialized
     * @return array
     */
    public function unserialize($serialized)
    {
        return unserialize($serialized);
    }

    /**
     * Return error messages that can be serialized by json_encode
     *
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->errorMessages;
    }
}
