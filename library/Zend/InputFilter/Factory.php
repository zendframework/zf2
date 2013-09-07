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
 *
 * This is used to programatically create input or input collection using a simple array
 * syntax. Some components like Zend\Form takes advantage of this feature.
 *
 * Each array specification can accept the following keys:
 *      - type: (string) name used to fetch the input from the input filter plugin manager
 *      - name: (string) name of the input
 *      - required: (bool) automatically add a NotEmpty validator to the Input
 *      - allow_empty: (bool) allow to validate an input even if empty
 *      - break_on_failure: (bool) if set to true and validation fails, does not validate next inputs
 *      - filters: (array) array of filters or array of filters specification
 *      - overwrite_filters: (bool) if true, the filters in "filters" key overwrite the one that may
 *                                  have been defined by default in the Input constructor
 *      - validators: (array) array of validators or array of validators specification
 *      - overwrite_validators: (bool) if true, the validators in "validators" key overwrite the one that may
 *                                     have been defined by default in the Input constructor
 *      - children: (array) allow to specify other specification for nested inputs
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
     * @param  array|Traversable $specification
     * @return InputInterface|InputCollectionInterface
     */
    public function createFromSpecification($specification)
    {
        if ($specification instanceof Traversable) {
            $specification = iterator_to_array($specification);
        }

        if (!isset($specification['type'])) {
            $specification['type'] = 'Zend\InputFilter\Input';
        }

        $inputOrInputCollection = $this->inputFilterPluginManager->get($specification['type']);

        unset($specification['type']);

        if ($inputOrInputCollection instanceof InputCollectionInterface) {
            return $this->createInputCollectionFromSpecification($inputOrInputCollection, $specification);
        }

        return $this->createInputFromSpecification($inputOrInputCollection, $specification);
    }

    /**
     * @param  InputInterface $input
     * @param  array          $specification
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
                    $input->setRequired($value);
                    break;
                case 'allow_empty':
                    $input->setAllowEmpty($value);
                    break;
                case 'break_on_failure':
                    $input->setBreakOnFailure($value);
                    break;
                case 'filters':
                    $overwriteFilters = isset($specification['overwrite_filters']) ? $specification['overwrite_filters'] : false;
                    $this->populateFilters($input, $value, $overwriteFilters);
                    break;
                case 'validators':
                    $overwriteValidators = isset($specification['overwrite_validators']) ? $specification['overwrite_validators'] : false;
                    $this->populateValidators($input, $value, $overwriteValidators);
                    break;
                default:
                    // Delegate any other option to a setter method, if any, so that custom
                    // input can have their own specific options
                    $method = 'set' . str_replace('_', '', $key);
                    if (method_exists($input, $method)) {
                        $input->$method($value);
                    }
            }
        }

        return $input;
    }

    /**
     * @param  InputCollectionInterface $inputCollection
     * @param  array                    $specification
     * @return InputCollectionInterface
     */
    protected function createInputCollectionFromSpecification(InputCollectionInterface $inputCollection, array $specification)
    {
        foreach ($specification as $key => $value) {
            switch($key) {
                case 'name':
                    $inputCollection->setName($value);
                    break;
                case 'break_on_failure':
                    $inputCollection->setBreakOnFailure($value);
                    break;
                case 'filters':
                    $overwriteFilters = isset($specification['overwrite_filters']) ? $specification['overwrite_filters'] : false;
                    $this->populateFilters($inputCollection, $value, $overwriteFilters);
                    break;
                case 'validators':
                    $overwriteValidators = isset($specification['overwrite_validators']) ? $specification['overwrite_validators'] : false;
                    $this->populateValidators($inputCollection, $value, $overwriteValidators);
                    break;
                case 'children':
                    foreach ($value as $child) {
                        $inputCollection->add($child);
                    }
                    break;
                default:
                    // Delegate any other option to a setter method, if any, so that custom
                    // input collection can have their own specific options
                    $method = 'set' . str_replace('_', '', $key);
                    if (method_exists($inputCollection, $method)) {
                        $inputCollection->$method($value);
                    }
            }
        }

        return $inputCollection;
    }

    /**
     * @param  InputInterface    $input
     * @param  array|Traversable $filters An array of FilterInterface or an array of filter specification
     * @param  bool              $overwrite If true, this replace the already existing filters
     * @return void
     * @throws Exception\RuntimeException
     */
    protected function populateFilters(InputInterface $input, $filters, $overwrite = false)
    {
        if ($filters instanceof Traversable) {
            $filters = iterator_to_array($filters);
        }

        $filterChain = $input->getFilterChain();

        if ($overwrite) {
            $filterChain->setFilters($filters);
        } else {
            $filterChain->addFilters($filters);
        }
    }

    /**
     * @param  InputInterface    $input
     * @param  array|Traversable $validators An array of ValidatorInterface or an array of validator specification
     * @param  bool              $overwrite If true, this replace the already existing validators
     * @return void
     * @throws Exception\RuntimeException
     */
    protected function populateValidators(InputInterface $input, $validators, $overwrite = false)
    {
        if ($validators instanceof Traversable) {
            $validators = iterator_to_array($validators);
        }

        $validatorChain = $input->getValidatorChain();

        if ($overwrite) {
            $validatorChain->setValidators($validators);
        } else {
            $validatorChain->addValidators($validators);
        }
    }
}
