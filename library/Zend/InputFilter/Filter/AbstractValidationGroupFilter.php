<?php
/**
 * Copyright (C) Maestrooo SAS - All Rights Reserved
 *
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * Written by Michaël Gallego <mic.gallego@gmail.com>
 */

namespace Zend\Filter\InputFilter;

use RecursiveFilterIterator;
use Zend\InputFilter\InputFilterInterface;

/**
 * Each custom validation group filter must extend this class
 */
abstract class AbstractValidationGroupFilter extends RecursiveFilterIterator
{
    /**
     * @param InputFilterInterface $inputFilter
     */
    public function __construct(InputFilterInterface $inputFilter)
    {
        parent::__construct($inputFilter);
    }
}
