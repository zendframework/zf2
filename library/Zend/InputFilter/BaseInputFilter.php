<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

use RecursiveIteratorIterator;

/**
 * Base input filter. This class expects concrete instance of InputInterface or InputFilterInterface. To allow
 * usage through a factory, please see the InputFilter class
 */
class BaseInputFilter implements InputFilterInterface
{
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
     * {@inheritDoc}
     */
    public function add($inputOrInputFilter, $name = null)
    {
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
        if (!isset($this->data[$name])) {
            // @TODO: throw exception
        }

        return $this->data[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function has($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($name)
    {
        unset($this->data[$name]);
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
     * {@inheritDoc}
     */
    public function setValidationGroup(array $validationGroup)
    {
        $this->validationGroup = $validationGroup;

        foreach ($this->children as $name => $inputOrInputFilter) {
            if ($inputOrInputFilter instanceof InputFilterInterface) {
                $inputOrInputFilter->setValidationGroup($validationGroup[$name]);
            }
        }

        // If a given key refers to another input filter, we give it the validation group

        /*foreach ($this->validationGroup as $key => $value) {
            if (isset($this->inputs[$key]) && $this->inputs[$key] instanceof InputFilterInterface) {
                $this->inputs[$key]->setValidationGroup($value);

                unset($this->validationGroup[$key]);
                $this->validationGroup[] = $key;
            }
        }*/
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
    public function isValid()
    {
        // Reset valid inputs, invalid inputs and error messages to allow this method to be called with
        // different set of data
        $this->validInputs = $this->invalidInputs = $this->errorMessages = array();

        $validationGroupFilter = new ValidationGroupFilter($this);
        $recursiveIterator     = new RecursiveIteratorIterator($validationGroupFilter, RecursiveIteratorIterator::LEAVES_ONLY);
        $valid                 = true;

        /** @var InputInterface $input */
        foreach ($recursiveIterator as $name => $input) {
            /** @var InputFilter $inputFilter */
            $inputFilter = $recursiveIterator->getSubIterator()->getInnerIterator();

            if ($input->isValid()) {
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
        return current($this->children);
    }
}
