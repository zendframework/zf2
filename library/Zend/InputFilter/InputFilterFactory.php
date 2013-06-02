<?php
/**
 * Copyright (C) Maestrooo SAS - All Rights Reserved
 *
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * Written by Michaël Gallego <mic.gallego@gmail.com>
 */

namespace Zend\InputFilter;

use Traversable;

/**
 * Input filter factory
 */
class InputFilterFactory
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
            return $this->createInputFromSpecificaiton($inputOrInputFilter, $specification);
        }

        return $this->createInputFilterFromSpecificatin($inputOrInputFilter, $specification);
    }

    /**
     * @param  InputInterface $input
     * @param  array $specification
     * @return InputInterface
     */
    protected function createInputFromSpecification(InputInterface $input, array $specification)
    {
        return $input;
    }

    /**
     * @param  InputFilterInterface $inputFilter
     * @param  array $specification
     * @return InputFilterInterface
     */
    protected function createInputFilterFromSpecification(InputFilterInterface $inputFilter, array $specification)
    {
        return $inputFilter;
    }
}
