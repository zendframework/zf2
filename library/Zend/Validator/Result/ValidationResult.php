<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator\Result;

/**
 * Simple class that holds data and error messages
 */
class ValidationResult implements ValidationResultInterface
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var array
     */
    protected $rawErrorMessages = array();

    /**
     * @var array
     */
    protected $errorMessages = array();

    /**
     * @var array
     */
    protected $messageVariables = array();

    /**
     * Constructor
     *
     * @param mixed $data
     * @param mixed $errorMessages
     * @param array $messageVariables
     */
    public function __construct($data, $errorMessages = array(), array $messageVariables = array())
    {
        $this->data             = $data;
        $this->rawErrorMessages = (array) $errorMessages;
        $this->messageVariables = $messageVariables;
    }

    /**
     * {@inheritDoc}
     */
    public function isValid()
    {
        return empty($this->rawErrorMessages);
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function getRawErrorMessages()
    {
        return $this->rawErrorMessages;
    }

    /**
     * {@inheritDoc}
     */
    public function getErrorMessages()
    {
        if (empty($this->errorMessages) || empty($this->messageVariables)) {
            return $this->errorMessages;
        }

        // We use simple regex here to inject variables into the error messages. Each variable
        // is surrounded by percent sign (eg.: %min%)
        $keys          = array_keys($this->messageVariables);
        $values        = array_values($this->messageVariables);

        foreach ($this->rawErrorMessages as $errorMessage) {
            $this->errorMessages[] = preg_replace($keys, $values, $errorMessage);
        }

        return $this->errorMessages;
    }

    /**
     * {@inheritDoc}
     */
    public function getMessageVariables()
    {
        return $this->messageVariables;
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
