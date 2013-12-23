<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter\ValidationGroup;

use RegexIterator;
use Zend\InputFilter\InputCollectionInterface;

/**
 * Validation group filter based on a regular expression
 */
class RegexFilterIterator implements ValidationGroupInterface
{
    /**
     * @var string
     */
    protected $regex;

    /**
     * @param string $regex
     */
    public function __construct($regex)
    {
        $this->regex = (string) $regex;
    }

    /**
     * {@inheritDoc}
     */
    public function createFilterIterator(InputCollectionInterface $inputCollection)
    {
        return new RegexFilterIterator(
            $inputCollection->getIterator(),
            $this->regex,
            RegexIterator::MATCH,
            RegexIterator::USE_KEY
        );
    }
}
