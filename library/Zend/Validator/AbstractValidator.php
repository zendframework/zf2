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
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Validator;

use Traversable;
use Zend\I18n\Translator\Translator;
use Zend\Registry;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\Exception\InvalidArgumentException;

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * The value to be validated
     *
     * @var mixed
     */
    protected $value;

    /**
     * Default translation object for all validate objects
     * @var Translator
     */
    protected static $defaultTranslator;

    /**
     * Limits the maximum returned length of a error message
     *
     * @var Integer
     */
    protected static $messageLength = -1;

    protected $abstractOptions = array(
        'messages'            => array(),  // Array of validation failure messages
        'message_templates'   => array(),  // Array of validation failure message templates
        'message_variables'   => array(),  // Array of additional variables available for validation failure messages
        'translator'          => null,     // Translation object to used -> Zend\I18n\Translator\Translator
        'translator_disabled' => false,    // Is translation disabled?
        'value_obscured'      => false,    // Flag indicating whether or not value should be obfuscated in error messages
    );

    /**
     * Abstract constructor for all validators
     * A validator should accept following parameters:
     *  - nothing f.e. Validator()
     *  - one or multiple scalar values f.e. Validator($first, $second, $third)
     *  - an array f.e. Validator(array($first => 'first', $second => 'second', $third => 'third'))
     *  - an instance of Traversable f.e. Validator($config_instance)
     *
     * @param array|Traversable $options
     */
    public function __construct($options = null)
    {
        // The abstract constructor allows no scalar values
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (isset($this->messageTemplates)) {
            $this->abstractOptions['message_templates'] = $this->messageTemplates;
        }

        if (isset($this->messageVariables)) {
            $this->abstractOptions['message_variables'] = $this->messageVariables;
        }

        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Returns an option
     *
     * @param string $option Option to be returned
     * @return mixed Returned option
     * @throws Exception\InvalidArgumentException
     */
    public function getOption($option)
    {
        if (array_key_exists($option, $this->abstractOptions)) {
            return $this->abstractOptions[$option];
        }

        if (isset($this->options) && array_key_exists($option, $this->options)) {
            return $this->options[$option];
        }

        throw new InvalidArgumentException("Invalid option '$option'");
    }

    /**
     * Returns all available options
     *
     * @return array Array with all available options
     */
    public function getOptions()
    {
        $result = $this->abstractOptions;
        if (isset($this->options)) {
            $result += $this->options;
        }
        return $result;
    }

    /**
     * Sets one or multiple options
     *
     * @param  array|Traversable $options Options to set
     * @throws Exception\InvalidArgumentException If $options is not an array or Traversable
     * @return AbstractValidator Provides fluid interface
     */
    public function setOptions($options = array())
    {
        if (!is_array($options) && !$options instanceof Traversable) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable');
        }

        foreach ($options as $name => $option) {
            $name = str_replace(' ', '', str_replace('_', ' ', $name));
            $fname = 'set' . ucfirst($name);
            $fname2 = 'is' . ucfirst($name);
            if (($name != 'setOptions') && method_exists($this, $name)) {
                $this->{$name}($option);
            } elseif (($fname != 'setOptions') && method_exists($this, $fname)) {
                $this->{$fname}($option);
            } elseif (($fname2 != 'setOptions') && method_exists($this, $fname2)) {
                $this->{$fname2}($option);
            } elseif (isset($this->options)) {
                $this->options[$name] = $option;
            } else {
                $this->abstractOptions[$name] = $options;
            }
        }

        return $this;
    }

    /**
     * Returns array of validation failure messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->abstractOptions['messages'];
    }

    /**
     * Invoke as command
     *
     * @param  mixed $value
     * @return boolean
     */
    public function __invoke($value)
    {
        return $this->isValid($value);
    }

    /**
     * Returns an array of the names of variables that are used in constructing validation failure messages
     *
     * @return array
     */
    public function getMessageVariables()
    {
        return array_keys($this->abstractOptions['message_variables']);
    }

    /**
     * Returns the message templates from the validator
     *
     * @return array
     */
    public function getMessageTemplates()
    {
        return $this->abstractOptions['message_templates'];
    }

    /**
     * Sets the validation failure message template for a particular key
     *
     * @param  string $messageString
     * @param  string $messageKey     OPTIONAL
     * @return AbstractValidator Provides a fluent interface
     * @throws Exception\InvalidArgumentException
     */
    public function setMessage($messageString, $messageKey = null)
    {
        if ($messageKey === null) {
            $keys = array_keys($this->abstractOptions['message_templates']);
            foreach($keys as $key) {
                $this->setMessage($messageString, $key);
            }
            return $this;
        }

        if (!isset($this->abstractOptions['message_templates'][$messageKey])) {
            throw new InvalidArgumentException("No message template exists for key '$messageKey'");
        }

        $this->abstractOptions['message_templates'][$messageKey] = $messageString;
        return $this;
    }

    /**
     * Sets validation failure message templates given as an array, where the array keys are the message keys,
     * and the array values are the message template strings.
     *
     * @param  array $messages
     * @return AbstractValidator
     */
    public function setMessages(array $messages)
    {
        foreach ($messages as $key => $message) {
            $this->setMessage($message, $key);
        }
        return $this;
    }

    /**
     * Magic function returns the value of the requested property, if and only if it is the value or a
     * message variable.
     *
     * @param  string $property
     * @return mixed
     * @throws Exception\InvalidArgumentException
     */
    public function __get($property)
    {
        if ($property == 'value') {
            return $this->value;
        }

        if (array_key_exists($property, $this->abstractOptions['message_variables'])) {
            $result = $this->abstractOptions['message_variables'][$property];
            if (is_array($result)) {
                $result = $this->{key($result)}[current($result)];
            } else {
                $result = $this->{$result};
            }
            return $result;
        }

        if (isset($this->messageVariables) && array_key_exists($property, $this->messageVariables)) {
            $result = $this->{$this->messageVariables[$property]};
            if (is_array($result)) {
                $result = $this->{key($result)}[current($result)];
            } else {
                $result = $this->{$result};
            }
            return $result;
        }

        throw new InvalidArgumentException("No property exists by the name '$property'");
    }

    /**
     * Constructs and returns a validation failure message with the given message key and value.
     *
     * Returns null if and only if $messageKey does not correspond to an existing template.
     *
     * If a translator is available and a translation exists for $messageKey,
     * the translation will be used.
     *
     * @param  string              $messageKey
     * @param  string|array|object $value
     * @return string
     */
    protected function createMessage($messageKey, $value)
    {
        if (!isset($this->abstractOptions['message_templates'][$messageKey])) {
            return null;
        }

        $message = $this->abstractOptions['message_templates'][$messageKey];

        $message = $this->translateMessage($messageKey, $message);

        if (is_object($value) &&
            !in_array('__toString', get_class_methods($value))
        ) {
            $value = get_class($value) . ' object';
        } elseif (is_array($value)) {
            $value = '[' . implode(', ', $value) . ']';
        } else {
            $value = (string)$value;
        }

        if ($this->isValueObscured()) {
            $value = str_repeat('*', strlen($value));
        }

        $message = str_replace('%value%', (string) $value, $message);
        foreach ($this->abstractOptions['message_variables'] as $ident => $property) {
            if (is_array($property)) {
                $value = $this->{key($property)}[current($property)];
                if (is_array($value)) {
                    $value = '[' . implode(', ', $value) . ']';
                }
            } else {
                $value = $this->$property;
            }
            $message = str_replace("%$ident%", (string)$value, $message);
        }

        $length = self::getMessageLength();
        if (($length > -1) && (strlen($message) > $length)) {
            $message = substr($message, 0, ($length - 3)) . '...';
        }

        return $message;
    }

    /**
     * @param  string $messageKey
     * @param  string $value      OPTIONAL
     * @return void
     */
    protected function error($messageKey, $value = null)
    {
        if ($messageKey === null) {
            $keys = array_keys($this->abstractOptions['message_templates']);
            $messageKey = current($keys);
        }

        if ($value === null) {
            $value = $this->value;
        }

        $this->abstractOptions['messages'][$messageKey] = $this->createMessage($messageKey, $value);
    }

    /**
     * Returns the validation value
     *
     * @return mixed Value to be validated
     */
    protected function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the value to be validated and clears the messages and errors arrays
     *
     * @param  mixed $value
     * @return void
     */
    protected function setValue($value)
    {
        $this->value               = $value;
        $this->abstractOptions['messages'] = array();
    }

    /**
     * Set flag indicating whether or not value should be obfuscated in messages
     *
     * @param  bool $flag
     * @return AbstractValidator
     */
    public function setValueObscured($flag)
    {
        $this->abstractOptions['value_obscured'] = (bool) $flag;
        return $this;
    }

    /**
     * Retrieve flag indicating whether or not value should be obfuscated in
     * messages
     *
     * @return bool
     */
    public function isValueObscured()
    {
        return $this->abstractOptions['value_obscured'];
    }

    /**
     * Set translation object
     *
     * @param  Translator|null $translator
     * @return AbstractValidator
     * @throws Exception\InvalidArgumentException
     */
    public function setTranslator(Translator $translator = null)
    {
        $this->abstractOptions['translator'] = $translator;
        return $this;
    }

    /**
     * Return translation object
     *
     * @return Translator|null
     */
    public function getTranslator()
    {
        if ($this->isTranslatorDisabled()) {
            return null;
        }

        if (null === $this->abstractOptions['translator']) {
            return self::getDefaultTranslator();
        }

        return $this->abstractOptions['translator'];
    }

    /**
     * Does this validator have its own specific translator?
     *
     * @return bool
     */
    public function hasTranslator()
    {
        return (bool)$this->abstractOptions['translator'];
    }

    /**
     * Set default translation object for all validate objects
     *
     * @param  Translator|null $translator
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public static function setDefaultTranslator(Translator $translator = null)
    {
        self::$defaultTranslator = $translator;
    }

    /**
     * Get default translation object for all validate objects
     *
     * @return Translator|null
     */
    public static function getDefaultTranslator()
    {
        return self::$defaultTranslator;
    }

    /**
     * Is there a default translation object set?
     *
     * @return boolean
     */
    public static function hasDefaultTranslator()
    {
        return (bool) self::$defaultTranslator;
    }

    /**
     * Indicate whether or not translation should be disabled
     *
     * @param  bool $flag
     * @return AbstractValidator
     */
    public function setTranslatorDisabled($flag)
    {
        $this->abstractOptions['translator_disabled'] = (bool) $flag;
        return $this;
    }

    /**
     * Is translation disabled?
     *
     * @return bool
     */
    public function isTranslatorDisabled()
    {
        return $this->abstractOptions['translator_disabled'];
    }

    /**
     * Returns the maximum allowed message length
     *
     * @return integer
     */
    public static function getMessageLength()
    {
        return self::$messageLength;
    }

    /**
     * Sets the maximum allowed message length
     *
     * @param integer $length
     */
    public static function setMessageLength($length = -1)
    {
        self::$messageLength = $length;
    }

    protected function translateMessage($messageKey, $message)
    {
        $translator = $this->getTranslator();
        if (!$translator) {
            return $message;
        }

        $translated = $translator->translate($messageKey);
        if ($translated !== $messageKey) {
            return $translated;
        }

        return $translator->translate($message);
    }
}
