<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

use FilterIterator;
use RecursiveIterator;

/**
 * Validation group filter
 */
class ValidationGroupFilter extends FilterIterator
{
    /**
     * @var array
     */
    protected $validationGroup;

    /**
     * @param RecursiveIterator $iterator
     * @param array $validationGroup
     */
    public function __construct(RecursiveIterator $iterator, array $validationGroup)
    {
        parent::__construct($iterator);

        // This is an optimization so that we can use isset instead of in_array when filtering (which
        // is much more efficient, especially if the array is large)
        $this->validationGroup = array_flip($validationGroup);
    }

    /**
     * {@inheritDoc}
     */
    public function accept()
    {
        return isset($this->validationGroup[$this->key()]);
    }
}
