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
 * Base input filter. This class expects concrete instance of InputInterface or InputFilterInterface. To allow
 * usage through a factory, please see the InputFilter class
 */
class InputFilter implements InputFilterInterface
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
     * @var InputFilterInterface[]|InputInterface[]
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
     * Get the input filter factory
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
    public function add($inputOrInputFilter, $name = null)
    {
        if (is_array($inputOrInputFilter) || $inputOrInputFilter instanceof Traversable) {
            $inputOrInputFilter = $this->factory->createFromSpecification($inputOrInputFilter);
        }

        if (null !== $name) {
            $inputOrInputFilter->setName($name);
        }

        $this->children[$inputOrInputFilter->getName()] = $inputOrInputFilter;
    }

    /**
     * {@inheritDoc}
     */
    public function get($name)
    {
        if (!isset($this->children[$name])) {
            throw new Exception\RuntimeException(sprintf(
                'No input or input filter named "%s" was found in input filter of type "%s" with the name "%s"',
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

        foreach ($this->children as $name => $inputOrInputFilter) {
            if ($inputOrInputFilter instanceof InputInterface) {
                $values[$name] = $inputOrInputFilter->getValue();
            } else {
                $values[$name] = $inputOrInputFilter->getValues();
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

        foreach ($this->children as $name => $inputOrInputFilter) {
            if ($inputOrInputFilter instanceof InputInterface) {
                $values[$name] = $inputOrInputFilter->getRawValue();
            } else {
                $values[$name] = $inputOrInputFilter->getRawValues();
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

        foreach ($this->children as $name => $inputOrInputFilter) {
            if ($inputOrInputFilter instanceof InputFilterInterface && isset($validationGroup[$name])) {
                $inputOrInputFilter->setValidationGroup($validationGroup[$name]);
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

        // The inner iterator must be an input filter interface, which is itself recursively iterable
        if (!$validationGroupFilter->getInnerIterator() instanceof InputFilterInterface) {
            throw new Exception\RuntimeException(
                'The validation group filter\'s inner recursive iterator must be an instance of Zend\InputFilter\InputFilterInterface, but "%s" given',
                get_class($validationGroupFilter->getInnerIterator())
            );
        }

        $recursiveIterator = new RecursiveIteratorIterator($validationGroupFilter, RecursiveIteratorIterator::LEAVES_ONLY);
        $valid             = true;

        /** @var InputInterface $input */
        foreach ($recursiveIterator as $name => $input) {
            /** @var InputFilter $inputFilter */
            $inputFilter = $recursiveIterator->getSubIterator()->getInnerIterator();

            $input->setValue(isset($inputFilter->data[$name]) ? $inputFilter->data[$name] : null);

            if ($input->isValid($context)) {
                $inputFilter->validInputs[$name] = $input;
                continue;
            }

            $inputFilter->invalidInputs[$name] = $input;
            $valid = false;

            $inputFilter->setErrorMessages($name, $input->getErrorMessages());

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
        return current($this->children) instanceof InputFilterInterface;
    }

    /**
     * {@inheritDoc}
     */
    public function getChildren()
    {
        /** @var InputFilterInterface $children */
        $children = current($this->children);
        $name     = $children->getName();

        // Lazily inject the data
        $children->setData(isset($this->data[$name]) ? $this->data[$name] : array());

        return $children;
    }
}
