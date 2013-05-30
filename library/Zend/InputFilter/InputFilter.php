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
 * Input filter
 */
class InputFilter implements InputFilterInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var InputFilterInterface[]|InputInterface[]
     */
    protected $inputs = array();

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

        $this->inputs[$inputOrInputFilter->getName()] = $inputOrInputFilter;
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
        if (isset($this->inputs[$name]) && $this->inputs[$name] instanceof InputInterface) {
            return $this->inputs[$name]->getValue();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getRawValue($name)
    {
        if (isset($this->inputs[$name]) && $this->inputs[$name] instanceof InputInterface) {
            return $this->inputs[$name]->getRawValue();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getValues()
    {
        $values = array();

        foreach ($this->inputs as $name => $inputOrInputFilter) {
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

        foreach ($this->inputs as $name => $inputOrInputFilter) {
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
    public function setValidationGroup(array $validationGroup)
    {
        $this->validationGroup = $validationGroup;

        foreach ($this->inputs as $name => $inputOrInputFilter) {
            if ($inputOrInputFilter instanceof InputFilterInterface) {
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
            $this->validationGroup = array_keys($this->inputs);
        }

        // If a given key refers to another input filter, we give it the validation group

        /*foreach ($this->validationGroup as $key => $value) {
            if (isset($this->inputs[$key]) && $this->inputs[$key] instanceof InputFilterInterface) {
                $this->inputs[$key]->setValidationGroup($value);

                unset($this->validationGroup[$key]);
                $this->validationGroup[] = $key;
            }
        }*/

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
        $validationGroupFilter = new ValidationGroupFilter($this, $this->getValidationGroup());
        $recursiveIterator     = new RecursiveIteratorIterator($validationGroupFilter, RecursiveIteratorIterator::LEAVES_ONLY);
        $valid                 = true;

        /** @var InputInterface $input */
        foreach ($recursiveIterator as $name => $input) {
            $inputFilter = $recursiveIterator->getSubIterator()->getInnerIterator();

            if ($input->isValid()) {
                continue;
            }

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
        return current($this->inputs);
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        next($this->inputs);
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return key($this->inputs);
    }

    /**
     * {@inheritDoc}
     */
    public function valid()
    {
        return current($this->inputs);
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        reset($this->inputs);
    }

    /**
     * {@inheritDoc}
     */
    public function hasChildren()
    {
        return current($this->inputs) instanceof InputFilterInterface;
    }

    /**
     * {@inheritDoc}
     */
    public function getChildren()
    {
        return current($this->inputs);
    }
}
