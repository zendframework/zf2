<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

use Zend\Validator\ValidatorChain;
use Zend\Filter\FilterChain;

/**
 * Input
 */
class Input implements InputInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $required = false;

    /**
     * @var bool
     */
    protected $allowEmpty = false;

    /**
     * @var bool
     */
    protected $breakOnFailure = false;

    /**
     * @var FilterChain
     */
    protected $filterChain;

    /**
     * @var ValidatorChain
     */
    protected $validatorChain;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var mixed
     */
    protected $fallbackValue;

    /**
     * @var array
     */
    protected $errorMessages;

    /**
     * @param FilterChain    $filterChain
     * @param ValidatorChain $validatorChain
     */
    public function __construct(FilterChain $filterChain, ValidatorChain $validatorChain)
    {
        $this->filterChain    = $filterChain;
        $this->validatorChain = $validatorChain;
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return $this->filterChain->filter($this->value);
    }

    /**
     * {@inheritDoc}
     */
    public function getRawValue()
    {
        return $this->value;
    }

    /**
     * {@inheritDoc}
     */
    public function setFallbackValue($fallbackValue)
    {
        $this->fallbackValue = $fallbackValue;
    }

    /**
     * {@inheritDoc}
     */
    public function getFallbackValue()
    {
        return $this->fallbackValue;
    }

    /**
     * {@inheritDoc}
     */
    public function setRequired($required)
    {
        if ($required) {
            // @TODO: add validator with high priority
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * {@inheritDoc}
     */
    public function setAllowEmpty($allowEmpty)
    {
        $this->allowEmpty = (bool) $allowEmpty;
    }

    /**
     * {@inheritDoc}
     */
    public function allowEmpty()
    {
        return $this->allowEmpty;
    }

    /**
     * {@inheritDoc}
     */
    public function setBreakOnFailure($breakOnFailure)
    {
        $this->breakOnFailure = (bool) $breakOnFailure;
    }

    /**
     * {@inheritDoc}
     */
    public function breakOnFailure()
    {
        return $this->breakOnFailure;
    }

    /**
     * {@inheritDoc}
     */
    public function setFilterChain(FilterChain $filterChain)
    {
        $this->filterChain = $filterChain;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilterChain()
    {
        return $this->filterChain;
    }

    /**
     * {@inheritDoc}
     */
    public function setValidatorChain(ValidatorChain $validatorChain)
    {
        $this->validatorChain = $validatorChain;
    }

    /**
     * {@inheritDoc}
     */
    public function getValidatorChain()
    {
        return $this->validatorChain;
    }

    /**
     * {@inheritDoc}
     */
    public function isValid($context = null)
    {
        if ($this->validatorChain->isValid($this->value, $context)) {
            return true;
        }

        if (empty($this->value)) {
            if ($this->fallbackValue) {
                $this->setValue($this->fallbackValue);
                return true;
            } elseif ($this->allowEmpty) {
                return true;
            }
        }

        $this->errorMessages = $this->validatorChain->getMessages();

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }
}
