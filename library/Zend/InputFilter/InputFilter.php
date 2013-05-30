<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

use RecursiveArrayIterator;

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
        // TODO: Implement getValue() method.
    }

    /**
     * {@inheritDoc}
     */
    public function getRawValue($name)
    {
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

        return $this->validationGroup;
    }

    /**
     * {@inheritDoc}
     */
    public function isValid()
    {
        $recursiveIterator     = new RecursiveArrayIterator($this->inputs);
        $validationGroupFilter = new ValidationGroupFilter($recursiveIterator, $this->getValidationGroup());

        foreach ($validationGroupFilter as $name => $inputOrInputFilter) {
            if (!$inputOrInputFilter->isValid()) {

            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }
}
