<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;

use Zend\Validator\Result\ValidationResult;

abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * An array of error message templates
     *
     * @var array
     */
    protected $messageTemplates = array();

    /**
     * An optional array of variables that are injected into the message templates
     *
     * @var array
     */
    protected $messageVariables = array();

    /**
     * Constructor for all validators
     *
     * It can accept an optional array of options, whose keys are underscore_separated
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    /**
     * Set the options
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $setter = 'set' . str_replace('_', '', $key);

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }

    /**
     * Set message templates
     *
     * @param array $messageTemplates
     */
    public function setMessageTemplates(array $messageTemplates)
    {
        $this->messageTemplates = $messageTemplates;
    }

    /**
     * Get message templates
     *
     * @return array
     */
    public function getMessageTemplates()
    {
        return $this->messageTemplates;
    }

    /**
     * Set message variables
     *
     * @param array $messageVariables
     */
    public function setMessageVariables(array $messageVariables)
    {
        $this->messageVariables = $messageVariables;
    }

    /**
     * Get message variables
     *
     * @return array
     */
    public function getMessageVariables()
    {
        return $this->messageVariables;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke($data, $context = null)
    {
        return $this->validate($data, $context);
    }

    /**
     * Build a validation result based on the error key
     *
     * @param  mixed        $data The data that failed validation
     * @param  string|array $keys The keys of the error message template
     * @throws Exception\InvalidArgumentException
     * @return Result\ValidationResultInterface
     */
    protected function buildErrorValidationResult($data, $keys)
    {
        // We cast to array to keep the same logic, as some validator may throw
        // two error messages
        $keys          = (array) $keys;
        $errorMessages = array();

        foreach ($keys as $key) {
            if (!isset($this->messageTemplates[$key])) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'No error message template was found for key "%s" in %s',
                    $key,
                    __CLASS__
                ));
            }

            $errorMessages[] = $this->messageTemplates[$key];
        }

        $variables = array();

        foreach ($this->messageVariables as $messageVariable) {
            $property    = str_replace('_', '', $messageVariable);
            $variableKey = '%' . $messageVariable . '%';

            $variables[$variableKey] = $this->$property;
        }

        return new ValidationResult($data, $errorMessages, $variables);
    }
}
