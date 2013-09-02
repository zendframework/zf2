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
 * Validation group filter based on a regular expression
 */
class RegexFilterIterator extends FilterIterator implements FilterIteratorInterface
{
    /**
     * @var string
     */
    protected $regex;

    /**
     * @param InputCollectionInterface $iterator
     * @param string                   $regex
     */
    public function __construct(InputCollectionInterface $iterator, $regex)
    {
        parent::__construct($iterator->getIterator());
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
