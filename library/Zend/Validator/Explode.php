<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;

use Traversable;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\ArrayUtils;

class Explode extends AbstractValidator implements ServiceLocatorAwareInterface
{
    const INVALID = 'explodeInvalid';

    protected $pluginManager;

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => "Invalid type given",
    );

    /**
     * @var array
     */
    protected $messageVariables = array();

    /**
     * @var string
     */
    protected $valueDelimiter = ',';

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var bool
     */
    protected $breakOnFirstFailure = false;

    /**
     * Sets the delimiter string that the values will be split upon
     *
     * @param string $delimiter
     * @return Explode
     */
    public function setValueDelimiter($delimiter)
    {
        $this->valueDelimiter = $delimiter;
        return $this;
    }

    /**
     * Returns the delimiter string that the values will be split upon
     *
     * @return string
     */
    public function getValueDelimiter()
    {
        return $this->valueDelimiter;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->pluginManager = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface|ValidatorPluginManager
     */
    public function getServiceLocator()
    {
        if (!$this->pluginManager) {
            $this->setServiceLocator(new ValidatorPluginManager());
        }

        return $this->pluginManager;
    }

    /**
     * Sets the Validator for validating each value
     *
     * @param ValidatorInterface|array $validator
     * @throws Exception\RuntimeException
     * @return Explode
     */
    public function setValidator($validator)
    {
        if (is_array($validator)) {
            if (!isset($validator['name'])) {
                throw new Exception\RuntimeException(
                    'Invalid validator specification provided; does not include "name" key'
                );
            }
            $name = $validator['name'];
            $options = isset($validator['options']) ?
                $validator['options'] : array();
            $validator = $this->getServiceLocator()->get($name, $options);
        }

        if (!$validator instanceof ValidatorInterface) {
            throw new Exception\RuntimeException(
                'Invalid validator given'
            );
        }

        $this->validator = $validator;
        return $this;
    }

    /**
     * Gets the Validator for validating each value
     *
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Set break on first failure setting
     *
     * @param  bool $break
     * @return Explode
     */
    public function setBreakOnFirstFailure($break)
    {
        $this->breakOnFirstFailure = (bool) $break;
        return $this;
    }

    /**
     * Get break on first failure setting
     *
     * @return bool
     */
    public function isBreakOnFirstFailure()
    {
        return $this->breakOnFirstFailure;
    }

    /**
     * Defined by Zend\Validator\ValidatorInterface
     *
     * Returns true if all values validate true
     *
     * @param  mixed $value
     * @return bool
     * @throws Exception\RuntimeException
     */
    public function isValid($value)
    {
        $this->setValue($value);

        if ($value instanceof Traversable) {
            $value = ArrayUtils::iteratorToArray($value);
        }

        if (is_array($value)) {
            $values = $value;
        } elseif (is_string($value)) {
            $delimiter = $this->getValueDelimiter();
            // Skip explode if delimiter is null,
            // used when value is expected to be either an
            // array when multiple values and a string for
            // single values (ie. MultiCheckbox form behavior)
            $values = (null !== $delimiter)
                      ? explode($this->valueDelimiter, $value)
                      : array($value);
        } else {
            $values = array($value);
        }

        $retval    = true;
        $messages  = array();
        $validator = $this->getValidator();

        if (!$validator) {
            throw new Exception\RuntimeException(sprintf(
                '%s expects a validator to be set; none given',
                __METHOD__
            ));
        }

        foreach ($values as $value) {
            if (!$validator->isValid($value)) {
                $messages[] = $validator->getMessages();
                $retval = false;

                if ($this->isBreakOnFirstFailure()) {
                    break;
                }
            }
        }

        $this->abstractOptions['messages'] = $messages;

        return $retval;
    }
}
