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
    protected $messageVariables = array();

    /**
     * Constructor
     *
     * @param mixed        $data
     * @param string|array $rawErrorMessages
     * @param array        $messageVariables
     */
    public function __construct($data, $rawErrorMessages = array(), array $messageVariables = array())
    {
        $this->data             = $data;
        $this->rawErrorMessages = (array) $rawErrorMessages;
        $this->messageVariables = $messageVariables;
    }

    /**
     * {@inheritDoc}
     */
    public function merge(ValidationResultInterface $validationResult)
    {
        // We don't want to "merge" data because a validation holds data for a single validator
        $this->rawErrorMessages = array_merge($this->rawErrorMessages, $validationResult->getRawErrorMessages());
        $this->messageVariables = array_merge($this->messageVariables, $validationResult->getMessageVariables());
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
        // If no message variables then this means no interpolation is needed, so we can return
        // raw error messages immediately
        if (empty($this->messageVariables)) {
            return $this->rawErrorMessages;
        }

        // We use simple regex here to inject variables into the error messages. Each variable
        // is surrounded by percent sign (eg.: %min%)
        $keys          = array_keys($this->messageVariables);
        $values        = array_values($this->messageVariables);
        $errorMessages = array();

        foreach ($this->rawErrorMessages as $rawErrorMessage) {
            $errorMessages[] = str_replace($keys, $values, $rawErrorMessage);
        }

        return $errorMessages;
    }

    /**
     * {@inheritDoc}
     */
    public function getMessageVariables()
    {
        return $this->messageVariables;
    }

    /**
     * Serialize the object
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            'data'               => $this->getData(),
            'raw_error_messages' => $this->getRawErrorMessages(),
            'message_variables'  => $this->getMessageVariables()
        ));
    }

    /**
     * Unserialize the object
     *
     * @param  string $serialized
     * @return void
     */
    public function unserialize($serialized)
    {
        $object = unserialize($serialized);

        $this->data             = $object['data'];
        $this->rawErrorMessages = $object['raw_error_messages'];
        $this->messageVariables = $object['message_variables'];
    }

    /**
     * Return error messages that can be serialized by json_encode
     *
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->getErrorMessages();
    }
}
