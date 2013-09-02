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
use Zend\InputFilter\Filter\AbstractValidationGroupFilter;
use Zend\InputFilter\Result\ValidationResult;

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
     * @var RecursiveFilterIterator
     */
    protected $validationGroupFilter;

    /**
     * @var array
     */
    protected $validationGroup = array();

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
     * @param  AbstractValidationGroupFilter $validationGroupFilter
     * @throws Exception\RuntimeException
     * @return void
     */
    public function setValidationGroupFilter(AbstractValidationGroupFilter $validationGroupFilter)
    {
        $this->validationGroupFilter = $validationGroupFilter;
    }

    /**
     * Get the validation group filter
     *
     * @return AbstractValidationGroupFilter
     */
    public function getValidationGroupFilter()
    {
        return $this->validationGroupFilter;
    }

    /**
     * {@inheritDoc}
     */
    public function validate(array $data = array(), $context = null)
    {
        $recursiveIterator         = $this->validationGroupFilter ?: $this;
        $recursiveIteratorIterator = new \RecursiveIteratorIterator($recursiveIterator);

        foreach ($recursiveIteratorIterator as $key) {
            var_dump($key);
        }
    }

    /**
     * Build a new validation result
     *
     * @param  array $data
     * @param  array $errorMessages
     * @return ValidationResult
     */
    protected function buildValidationResult(array $data, array $errorMessages)
    {
        // By convention, we assume that if data is valid, user want the filter data,
        // so we filter it automatically, otherwise, we don't filter anything
        if (!empty($errorMessages)) {
            // There are errors!
            return new ValidationResult($data, array(), $errorMessages);
        }

        return new ValidationResult($data, $data, $errorMessages);
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
        return current($this->children);
    }
}
