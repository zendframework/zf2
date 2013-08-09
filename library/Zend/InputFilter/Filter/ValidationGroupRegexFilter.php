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
 * Validation group filter based on a Regex. The validation group array that may be defined in
 * the input collection is therefore silently ignored
 */
class ValidationGroupRegexFilter extends RecursiveFilterIterator
{
    /**
     * @var string
     */
    protected $regex;

    /**
     * @param RecursiveIterator $iterator
     * @param string            $regex
     */
    public function __construct(RecursiveIterator $iterator, $regex)
    {
        parent::__construct($iterator);
        $this->regex = (string) $regex;
    }

    /**
     * {@inheritDoc}
     */
    public function accept()
    {
        return (bool) preg_match($this->regex, $this->key());
    }
}
