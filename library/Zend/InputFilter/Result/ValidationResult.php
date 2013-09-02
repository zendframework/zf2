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
     * @var mixed
     */
    protected $rawValue;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var array
     */
    protected $errorMessages = array();

    /**
     * @param mixed $rawValue
     * @param mixed $value
     * @param array $errorMessages
     */
    public function __construct($rawValue, $value = null, array $errorMessages = array())
    {
        $this->rawValue      = $rawValue;
        $this->value         = $value;
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
    public function getValue()
    {
        $this->value;
    }

    /**
     * {@inheritDoc}
     */
    public function getRawValue()
    {
        $this->rawValue;
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
