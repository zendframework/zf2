<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter\InputFilter;

use RecursiveFilterIterator;
use RecursiveIterator;

/**
 * Validation group filter based on a simple array defined in the input filter
 */
class ValidationGroupArrayFilter extends RecursiveFilterIterator
{
    /**
     * @var array
     */
    protected $validationGroup;

    /**
     * @param RecursiveIterator $iterator
     * @param array             $validationGroup
     */
    public function __construct(RecursiveIterator $iterator, array $validationGroup)
    {
        parent::__construct($iterator);

        // This is an optimization, this way we can check using isset, which is way faster than
        // in_array (especially with very large arrays)
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
