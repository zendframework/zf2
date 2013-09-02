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
use Zend\Filter\FilterChain;
use Zend\InputFilter\Result\ValidationResult;
use Zend\InputFilter\ValidationGroup\FilterIteratorInterface;
use Zend\InputFilter\ValidationGroup\NoOpFilterIterator;
use Zend\Validator\ValidatorChain;

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
     * @param FilterChain|null    $filterChain
     * @param ValidatorChain|null $validatorChain
     * @param Factory|null        $factory
     */
    public function __construct(
        FilterChain $filterChain = null,
        ValidatorChain $validatorChain = null,
        Factory $factory = null
    ) {
        parent::__construct($filterChain, $validatorChain);
        $this->factory = $factory ?: new Factory(new InputFilterPluginManager());
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
        $iterator         = $this->getValidationGroupFilter();
        $iteratorIterator = new IteratorIterator($iterator);
        $errorMessages    = array();

        // As an input collection can have also validators and filters, we first apply the
        // validation for itself
        if (!$this->validatorChain->isValid($data, $context)) {
            $errorMessages[$this->name] = $this->validatorChain->getMessages();
        }

        /** @var InputInterface|InputCollectionInterface $inputOrInputCollection */
        foreach ($iteratorIterator as $inputOrInputCollection) {
            $name  = $inputOrInputCollection->getName();
            $value = isset($data[$name]) ? $data[$name] : null;

            if ($inputOrInputCollection instanceof InputCollectionInterface && null !== $value) {
                // @TODO: in current ZF2 implementation, if an input inside a nested input filter
                // @TODO  is configured to break on failure, it only break from the current input filter.
                // @TODO  Should we throw an exception and allow to break all other inputs, even if not
                // @TODO  at same nested level?
                $validationResult = $inputOrInputCollection->validate($value, $context);

                if ($validationResult->isValid()) {
                    $errorMessages[$name] = $validationResult->getErrorMessages();
                }

                continue;
            }

            // Otherwise we have an input
            $validationResult = $inputOrInputCollection->validate($value, $context);

            if (!$validationResult->isValid()) {
                $errorMessages[$name] = $validationResult->getErrorMessages();

                if ($inputOrInputCollection->breakOnFailure()) {
                    break;
                }
            }
        }

        return $this->buildValidationResult($data, $errorMessages);
    }

    /**
     * Build a validation result from the raw data and error messages
     *
     * By default, this method assumes that if there are NO ERRORS in the given
     * input collection (ie. all inputs have passed validation), then the data
     * are filtered. Otherwise, they are not
     *
     * @param  array $rawData
     * @param  array $errorMessages
     * @return Result\ValidationResultInterface
     */
    protected function buildValidationResult(array $rawData, array $errorMessages)
    {
        if (!empty($errorMessages)) {
            return new ValidationResult($rawData, null, $errorMessages);
        }

        // Otherwise, we filter data only for the given input collection
        $iterator         = $this->getValidationGroupFilter();
        $iteratorIterator = new IteratorIterator($iterator);
        $filteredData     = array();

        /** @var InputInterface|InputCollectionInterface $inputOrInputCollection */
        foreach ($iteratorIterator as $inputOrInputCollection) {
            $name = $inputOrInputCollection->getName();
            $data = isset($rawData[$name]) ? $rawData[$name] : null;

            $filteredData[$name] = $inputOrInputCollection->getFilterChain()->filter($data);
        }

        // As an input collection can also contain filters, we finally filter the data
        // using the input collection filters
        $this->filterChain->filter($filteredData);

        return new ValidationResult($rawData, $filteredData, $errorMessages);
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
