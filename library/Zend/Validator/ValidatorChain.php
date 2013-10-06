<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;

use Countable;
use Zend\Stdlib\PriorityQueue;
use Zend\Validator\Result\ValidationResult;

/**
 * A validator chain that allows to execute multiple validators one after the other
 */
class ValidatorChain implements ValidatorInterface, Countable
{
    /**
     * Default priority at which validators are added
     */
    const DEFAULT_PRIORITY = 1;

    /**
     * Validator plugin manager that is used to add validators by name
     *
     * @var ValidatorPluginManager
     */
    protected $validatorPluginManager;

    /**
     * validator chain
     *
     * @var PriorityQueue|ValidatorInterface[]
     */
    protected $validators;

    /**
     * Constructor
     */
    public function __construct(ValidatorPluginManager $validatorPluginManager)
    {
        $this->validatorPluginManager = $validatorPluginManager;
        $this->validators             = new PriorityQueue();
    }

    /**
     * Return the count of attached validators
     *
     * @return int
     */
    public function count()
    {
        return count($this->validators);
    }

    /**
     * Attach a validator to the chain
     *
     * @param  ValidatorInterface|callable $validator A Validator implementation or valid PHP callback
     * @param  int                         $priority Priority at which to enqueue validator; defaults to 1 (higher executes earlier)
     * @return void
     */
    public function attach(callable $validator, $priority = self::DEFAULT_PRIORITY)
    {
        $this->validators->insert($validator, $priority);
    }

    /**
     * Remove a validator from the chain
     *
     * Note that this method needs to iterate through all the validators, so it can be slow
     *
     * @param  ValidatorInterface|callable $validator
     * @return bool True if the validator was successfully removed, false otherwise
     */
    public function remove(callable $validator)
    {
        foreach ($this->validators as $key => $value) {
            if ($validator === $value) {
                unset($this->validators[$key]);
                return true;
            }
        }

        return false;
    }

    /**
     * Attach a validator to the chain by its name (using the validator plugin manager)
     *
     * @param  string $name Valid name
     * @param  array  $options Optional options
     * @param  int    $priority Priority at which to enqueue validator; defaults to 1 (higher executes earlier)
     * @return void
     */
    public function attachByName($name, array $options = array(), $priority = self::DEFAULT_PRIORITY)
    {
        $validator = $this->validatorPluginManager->get($name, $options);
        $this->validators->insert($validator, $priority);
    }

    /**
     * Merge the validator chain with the one given in parameter
     *
     * @param  ValidatorChain $validatorChain
     * @return void
     */
    public function merge(ValidatorChain $validatorChain)
    {
        foreach ($validatorChain->validators->toArray(PriorityQueue::EXTR_BOTH) as $item) {
            $this->attach($item['data'], $item['priority']);
        }
    }

    /**
     * Add validators to the validator chain
     *
     * @param array $validators
     * @return void
     */
    public function addValidators(array $validators)
    {
        foreach ($validators as $validator) {
            if ($validator instanceof ValidatorInterface) {
                $this->attach($validator);
            } elseif (is_array($validator)) {
                $options  = isset($validator['options']) ? $validator['options'] : array();
                $priority = isset($validator['priority']) ? $validator['priority'] : self::DEFAULT_PRIORITY;

                $this->attachByName($validator['name'], $options, $priority);
            }
        }
    }

    /**
     * Set validators using concrete instances or specification
     *
     * @param array[]|ValidatorInterface[] $validators
     * @return void
     */
    public function setValidators(array $validators)
    {
        $this->validators = new PriorityQueue();
        $this->addValidators($validators);
    }

    /**
     * Get all the validators
     *
     * @return PriorityQueue|ValidatorInterface[]
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * Validate the value against all validators. Validators are run according to priority
     *
     * {@inheritDoc}
     */
    public function validate($data, $context = null)
    {
        $validationResult = new ValidationResult($data);

        foreach ($this->validators as $validator) {
            $innerValidationResult = $validator->validate($data, $context);

            if (!$innerValidationResult->isValid()) {
                $validationResult->merge($innerValidationResult);
            }
        }

        return $validationResult;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke($data, $context = null)
    {
        return $this->validate($data, $context);
    }

    /**
     * Clone validators
     */
    public function __clone()
    {
        $this->validators = clone $this->validators;
    }
}
