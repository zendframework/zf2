<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

use Traversable;
use Zend\Filter\FilterChain;
use Zend\Validator\ValidatorChain;

/**
 * Input filter factory
 */
class Factory
{
    /**
     * @var InputFilterPluginManager
     */
    protected $inputFilterPluginManager;

    /**
     * @param InputFilterPluginManager $inputFilterPluginManager
     */
    public function __construct(InputFilterPluginManager $inputFilterPluginManager)
    {
        $this->inputFilterPluginManager = $inputFilterPluginManager;
    }

    /**
     * @param array|Traversable $specification
     * @return InputInterface|InputFilterInterface
     */
    public function createFromSpecification($specification)
    {
        if ($specification instanceof Traversable) {
            $specification = iterator_to_array($specification);
        }

        if (!isset($specification['type'])) {
            $specification['type'] = 'Zend\InputFilter\Input';
        }

        $inputOrInputFilter = $this->inputFilterPluginManager->get($specification['type']);

        if ($inputOrInputFilter instanceof InputInterface) {
            return $this->createInputFromSpecification($inputOrInputFilter, $specification);
        }

        return $this->createInputFilterFromSpecification($inputOrInputFilter, $specification);
    }

    /**
     * @param  InputInterface $input
     * @param  array $specification
     * @return InputInterface
     */
    protected function createInputFromSpecification(InputInterface $input, array $specification)
    {
        foreach ($specification as $key => $value) {
            switch($key) {
                case 'name':
                    $input->setName($value);
                    break;
                case 'required':
                    $input->setRequired(true);
                    break;
                case 'allow_empty':
                    $input->setAllowEmpty(true);
                    break;
                case 'fallback_value':
                    $input->setFallbackValue($value);
                    break;
                case 'filters':
                    $this->populateFilters($input, $value);
                    break;
                case 'validators':
                    $this->populateValidators($input, $value);
                    break;
            }
        }

        return $input;
    }

    /**
     * @param  InputFilterInterface $inputFilter
     * @param  array $specification
     * @return InputFilterInterface
     */
    protected function createInputFilterFromSpecification(InputFilterInterface $inputFilter, array $specification)
    {
        foreach ($specification as $key => $value) {
            switch($key) {
                // @TODO: todo
            }
        }

        return $inputFilter;
    }

    /**
     * @param  InputInterface          $input
     * @param  FilterChain|Traversable $filters
     * @return void
     * @throws Exception\RuntimeException
     */
    protected function populateFilters(InputInterface $input, $filters)
    {
        if ($filters instanceof FilterChain) {
            $input->setFilterChain($filters);
            return;
        }

        if (!is_array($filters) && !$filters instanceof Traversable) {
            throw new Exception\RuntimeException(sprintf(
                '%s expects the value associated with "filters" to be an array/Traversable of filters or filters specifications, or a FilterChain; received "%s"',
                __METHOD__,
                (is_object($filters) ? get_class($filters) : gettype($filters))
            ));
        }

        $filterChain = $input->getFilterChain();

        foreach ($filters as $filterSpecification) {
            // @TODO: delegate to a filter factory
        }
    }

    /**
     * @param  InputInterface             $input
     * @param  ValidatorChain|Traversable $validators
     * @return void
     * @throws Exception\RuntimeException
     */
    protected function populateValidators(InputInterface $input, $validators)
    {
        if ($validators instanceof ValidatorChain) {
            $input->setValidatorChain($validators);
            return;
        }

        if (!is_array($validators) && !$validators instanceof Traversable) {
            throw new Exception\RuntimeException(sprintf(
                '%s expects the value associated with "validators" to be an array/Traversable of validators or validators specifications, or a ValidatorChain; received "%s"',
                __METHOD__,
                (is_object($validators) ? get_class($validators) : gettype($validators))
            ));
        }

        $validatorChain = $input->getValidatorChain();

        foreach ($validators as $validatorSpecification) {
            // @TODO: delegate to a validator factory
        }
    }
}
