<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

use IteratorIterator;
use Zend\InputFilter\Result\ValidationResult;
use Zend\InputFilter\ValidationGroup\FilterIteratorInterface;
use Zend\InputFilter\ValidationGroup\NoOpFilterIterator;

/**
 * Input collection class
 */
class InputCollection extends Input implements InputCollectionInterface
{
    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var InputCollectionInterface[]|InputInterface[]
     */
    protected $children = array();

    /**
     * @var FilterIteratorInterface
     */
    protected $validationGroupFilter;

    /**
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Get the input collection factory
     *
     * @return Factory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * {@inheritDoc}
     */
    public function add($inputOrInputCollection, $name = null)
    {
        // Note: you MUST NOT check against Traversable here, because InputCollection is a Traversable itself
        if (is_array($inputOrInputCollection)) {
            $inputOrInputCollection = $this->factory->createFromSpecification($inputOrInputCollection);
        }

        if (null !== $name) {
            $inputOrInputCollection->setName($name);
        }

        $this->children[$inputOrInputCollection->getName()] = $inputOrInputCollection;
    }

    /**
     * {@inheritDoc}
     */
    public function get($name)
    {
        if (!isset($this->children[$name])) {
            throw new Exception\RuntimeException(sprintf(
                'No input or input collection named "%s" was found in input collection of type "%s" with the name "%s"',
                $name,
                __CLASS__,
                $this->getName()
            ));
        }

        return $this->children[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function has($name)
    {
        return isset($this->children[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($name)
    {
        unset($this->children[$name]);
    }

    /**
     * Set a validation group filter
     *
     * @param  FilterIteratorInterface $validationGroupFilter
     * @throws Exception\RuntimeException
     * @return void
     */
    public function setValidationGroupFilter(FilterIteratorInterface $validationGroupFilter)
    {
        $this->validationGroupFilter = $validationGroupFilter;
    }

    /**
     * Get the validation group filter
     *
     * @return FilterIteratorInterface
     */
    public function getValidationGroupFilter()
    {
        if (null === $this->validationGroupFilter) {
            $this->validationGroupFilter = new NoOpFilterIterator($this);
        }

        return $this->validationGroupFilter;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($data, $context = null)
    {
        $errorMessages = $this->buildErrorMessages($data, $context);

        return $this->buildValidationResult($data, $errorMessages);
    }

    /**
     * Build error messages (without creating a validation result)
     *
     * This method should only be used internally by the "validate" method to recursively
     * validating nested input collections
     *
     * @param  array      $data
     * @param  mixed|null $context
     * @return array
     */
    protected function buildErrorMessages(array $data, $context = null)
    {
        $iterator         = $this->getValidationGroupFilter();
        $iteratorIterator = new IteratorIterator($iterator);
        $errorMessages    = array();

        /** @var InputInterface|InputCollectionInterface $inputOrInputCollection */
        foreach ($iteratorIterator as $inputOrInputCollection) {
            $name = $inputOrInputCollection->getName();

            if ($inputOrInputCollection instanceof InputCollectionInterface && isset($data[$name])) {
                // @TODO: in current ZF2 implementation, if an input inside a nested input filter
                // @TODO  is configured to break on failure, it only break from the current input filter.
                // @TODO  Should we throw an exception and allow to break all other inputs, even if not
                // @TODO  at same nested level?
                $inputCollectionErrors = $inputOrInputCollection->buildErrorMessages($data[$name]);

                if (!empty($inputCollectionErrors)) {
                    $errorMessages[$name] = $inputCollectionErrors;
                }

                continue;
            }

            // Otherwise we have an input
            $value            = isset($data[$name]) ? $data[$name] : null;
            $validationResult = $inputOrInputCollection->validate($value, $context);

            if (!$validationResult->isValid()) {
                $errorMessages[$name] = $validationResult->getErrorMessages();

                if ($inputOrInputCollection->breakOnFailure()) {
                    break;
                }
            }
        }

        return $errorMessages;
    }

    /**
     * Build a validation result from the raw data and error messages
     *
     * By default, this method assumes that if there are NO ERRORS (ie. all inputs have
     * passed validation), then the user is likely to get filtered values, so all values
     * are filtered. Otherwise, for saving cycles, if there are errors, values are
     * not filtered at all, and only raw data are given to the validation result
     *
     * @param  array $rawData
     * @param  array $errorMessages
     * @return Result\ValidationResultInterface
     */
    protected function buildValidationResult(array $rawData, array $errorMessages)
    {
        if (!empty($errorMessages)) {
            return new ValidationResult($rawData, array(), $errorMessages);
        }

        // @TODO: filter data efficiently
        return new ValidationResult($rawData, $rawData, $errorMessages);
    }

    /**
     * --------------------------------------------------------------------------------
     * Implementation of Iterator interface
     * --------------------------------------------------------------------------------
     */

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return current($this->children);
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        next($this->children);
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return key($this->children);
    }

    /**
     * {@inheritDoc}
     */
    public function valid()
    {
        return current($this->children);
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        reset($this->children);
    }
}
