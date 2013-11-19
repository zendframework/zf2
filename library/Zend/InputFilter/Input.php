<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

use Zend\InputFilter\Result\InputFilterResult;
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
     * @param FilterChain|null    $filterChain
     * @param ValidatorChain|null $validatorChain
     */
    public function __construct(FilterChain $filterChain = null, ValidatorChain $validatorChain = null)
    {
        $this->filterChain    = $filterChain ?: new FilterChain();
        $this->validatorChain = $validatorChain ?: new ValidatorChain();
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        $this->name = (string) $name;
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
    public function setRequired($required)
    {
        if ($required) {
            $this->required = true;

            $this->validatorChain->attachByName(
                'Zend\Validator\NotEmpty',
                array(),
                self::REQUIRED_VALIDATOR_PRIORITY
            );
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
    public function getFilterChain()
    {
        return $this->filterChain;
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
    public function runAgainst($value, $context = null)
    {
        $filteredValue = $this->filterChain->filter($value);

        if (
            $this->validatorChain->isValid($filteredValue, $context)
            || (empty($filteredValue) && $this->allowEmpty)
        ) {
            return new InputFilterResult($value, $filteredValue);
        }

        // If it is not valid, we don't want to store the filtered value, as it's
        // incorrect anyway...
        return new InputFilterResult($value, null, $this->validatorChain->getMessages());
    }
}
