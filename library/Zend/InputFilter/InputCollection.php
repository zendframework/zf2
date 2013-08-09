<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

use RecursiveFilterIterator;
use RecursiveIteratorIterator;
use Traversable;
use Zend\Filter\InputFilter\ValidationGroupArrayFilter;

/**
 * Input collection class
 */
class InputCollection implements InputCollectionInterface
{
    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var InputCollectionInterface[]|InputInterface[]
     */
    protected $children = array();

    /**
     * @var InputInterface[]
     */
    protected $validInputs = array();

    /**
     * @var InputInterface[]
     */
    protected $invalidInputs = array();

    /**
     * @var RecursiveFilterIterator
     */
    protected $validationGroupFilter;

    /**
     * @var array
     */
    protected $validationGroup = array();

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var array
     */
    protected $errorMessages = array();

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
        if (is_array($inputOrInputCollection) || $inputOrInputCollection instanceof Traversable) {
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
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue($name)
    {
        if (isset($this->children[$name]) && $this->children[$name] instanceof InputInterface) {
            return $this->children[$name]->getValue();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getRawValue($name)
    {
        if (isset($this->children[$name]) && $this->children[$name] instanceof InputInterface) {
            return $this->children[$name]->getRawValue();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getValues()
    {
        $values = array();

        foreach ($this->children as $name => $inputOrInputCollection) {
            if ($inputOrInputCollection instanceof InputInterface) {
                $values[$name] = $inputOrInputCollection->getValue();
            } else {
                $values[$name] = $inputOrInputCollection->getValues();
            }
        }

        return $values;
    }

    /**
     * {@inheritDoc}
     */
    public function getRawValues()
    {
        $values = array();

        foreach ($this->children as $name => $inputOrInputCollection) {
            if ($inputOrInputCollection instanceof InputInterface) {
                $values[$name] = $inputOrInputCollection->getRawValue();
            } else {
                $values[$name] = $inputOrInputCollection->getRawValues();
            }
        }

        return $values;
    }

    /**
     * {@inheritDoc}
     */
    public function getValidInputs()
    {
        return $this->validInputs;
    }

    /**
     * {@inheritDoc}
     */
    public function getInvalidInputs()
    {
        return $this->invalidInputs;
    }

    /**
     * @param  RecursiveFilterIterator $validationGroupFilter
     * @return void
     */
    public function setValidationGroupFilter(RecursiveFilterIterator $validationGroupFilter)
    {
        $this->validationGroupFilter = $validationGroupFilter;
    }

    /**
     * @return RecursiveFilterIterator
     */
    public function getValidationGroupFilter()
    {
        if (null === $this->validationGroupFilter) {
            $this->validationGroupFilter = new ValidationGroupArrayFilter($this, $this->getValidationGroup());
        }

        return $this->validationGroupFilter;
    }

    /**
     * {@inheritDoc}
     */
    public function setValidationGroup(array $validationGroup)
    {
        $this->validationGroup = $validationGroup;

        foreach ($this->children as $name => $inputOrInputCollection) {
            if ($inputOrInputCollection instanceof InputCollectionInterface && isset($validationGroup[$name])) {
                $inputOrInputCollection->setValidationGroup($validationGroup[$name]);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getValidationGroup()
    {
        // If validation group is empty, we assume to validate everything
        if (empty($this->validationGroup)) {
            $this->validationGroup = array_keys($this->children);
        }

        return $this->validationGroup;
    }

    /**
     * {@inheritDoc}
     */
    public function setErrorMessages($name, array $errorMessages)
    {
        $this->errorMessages[$name] = $errorMessages;
    }

    /**
     * {@inheritDoc}
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * {@inheritDoc}
     */
    public function isValid($context = null)
    {
        // Reset valid inputs, invalid inputs and error messages to allow this method to be called with
        // different set of data
        $this->validInputs = $this->invalidInputs = $this->errorMessages = array();

        $validationGroupFilter = $this->getValidationGroupFilter();

        // The inner iterator must be an input collection interface, which is itself recursively iterable
        if (!$validationGroupFilter->getInnerIterator() instanceof InputCollectionInterface) {
            throw new Exception\RuntimeException(
                'The validation group filter\'s inner recursive iterator must be an instance of Zend\InputFilter\InputCollectionInterface, but "%s" given',
                get_class($validationGroupFilter->getInnerIterator())
            );
        }

        $recursiveIterator = new RecursiveIteratorIterator($validationGroupFilter, RecursiveIteratorIterator::LEAVES_ONLY);
        $valid             = true;

        /** @var InputInterface $input */
        foreach ($recursiveIterator as $name => $input) {
            /** @var InputCollection $inputCollection */
            $inputCollection = $recursiveIterator->getSubIterator()->getInnerIterator();

            $input->setValue(isset($inputCollection->data[$name]) ? $inputCollection->data[$name] : null);

            if ($input->isValid($context)) {
                $inputCollection->validInputs[$name] = $input;
                continue;
            }

            $inputCollection->invalidInputs[$name] = $input;
            $valid = false;

            $inputCollection->setErrorMessages($name, $input->getErrorMessages());

            if ($input->breakOnFailure()) {
                return false;
            }
        }

        return $valid;
    }

    /**
     * --------------------------------------------------------------------------------
     * Implementation of RecursiveIterator interface
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

    /**
     * {@inheritDoc}
     */
    public function hasChildren()
    {
        return current($this->children) instanceof InputCollectionInterface;
    }

    /**
     * {@inheritDoc}
     */
    public function getChildren()
    {
        /** @var InputCollectionInterface $children */
        $children = current($this->children);
        $name     = $children->getName();

        // Lazily inject the data
        $children->setData(isset($this->data[$name]) ? $this->data[$name] : array());

        return $children;
    }
}
