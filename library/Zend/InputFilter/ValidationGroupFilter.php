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
use RecursiveIterator;

/**
 * Validation group filter
 */
class ValidationGroupFilter extends RecursiveFilterIterator
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
        $this->validationGroup = $validationGroup;
    }

    /**
     * {@inheritDoc}
     */
    public function accept()
    {
        return in_array($this->key(), $this->validationGroup);
    }
}
