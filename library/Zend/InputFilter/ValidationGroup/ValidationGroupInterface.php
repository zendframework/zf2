<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter\ValidationGroup;

use FilterIterator;
use Zend\InputFilter\InputCollectionInterface;

/**
 * Base interface for all input collection validation groups
 */
interface ValidationGroupInterface
{
    /**
     * Create the SPL filter iterator to be used with the given input collection
     *
     * @param  InputCollectionInterface $inputCollection
     * @return FilterIterator
     */
    public function createFilterIterator(InputCollectionInterface $inputCollection);
}
